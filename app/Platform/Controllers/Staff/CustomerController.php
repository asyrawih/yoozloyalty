<?php
namespace Platform\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use App\Staff;
use Platform\Models\Campaign;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * @group Staff find Customer
 *
 * Endpoint for retrieving customer by phone number
 */
class CustomerController extends Controller
{
    const allowedImageFileExtension = ['png','jpg','jpeg'];

    /*
     |--------------------------------------------------------------------------
     | Customers related functions
     |--------------------------------------------------------------------------
     */

    /**
     * Get customer list.
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCustomers(Request $request)
    {

		$campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

		$staff = Staff::withoutGlobalScopes()->where('id', Auth::user('staff')->id)->firstOrFail();

		if($request->has('search') && $request->query('search') != '') {
			$search = strip_tags($request->search);
			$customers = Customer::select(
				'id',
				'campaign_id',
				'name',
				'email',
				'customer_number',
				'card_number',
				'last_login',
				'uuid',
                'country_code'
			)->whereRaw("
				(
					customer_number LIKE '$search%'
						AND
					campaign_id = $campaign->id
				) OR
				(
					name LIKE '$search%'
						AND
					campaign_id = $campaign->id
				) OR
				(
					email LIKE '$search%'
						AND
					campaign_id = $campaign->id
				) OR
				(
					card_number LIKE '$search%'
						AND
					campaign_id = $campaign->id
				)
			")->get();
		} else {
			$customers = Customer::select('id','campaign_id','name','email','customer_number','card_number','last_login','uuid', 'country_code')
								 ->where('campaign_id', $campaign->id)
								 ->get();
		}

		$timezone = $staff->getTimezone();
		if(!$timezone) {
			$timezone = 'UTC';
		}

		$customers = $customers->map(function ($record) use ($timezone) {
			if($record->last_login) {
				$record->last_login = $record->last_login->timezone($timezone);
			}
			return collect($record)->only('name', 'email', 'last_login', 'uuid', 'card', 'number');
		});

		return response()->json($customers);
    }

	/**
     * Save post
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function postCreateCustomer(Request $request)
	{


		$model = 'App\\Customer';
		$uuid = ($request->uuid == 'null') ? null: $request->uuid;
		$request->formModel = (array) json_decode($request->formModel);

		if($request->uuid == null) {
		  $user = auth()->user();
		  if($user->role != 1) {

			$checkingPlanLimitation = Plan::checkingUserPlanLimitations(
			  auth()->user(), $model
			);

			if($checkingPlanLimitation['error']) {
			  return response()->json([
				'status' => 'error',
				'msg' => $checkingPlanLimitation['message']
			  ], 422);
			}
		  }
		}

		if (! class_exists($model)) {
		  return response()->json(['status' => 'error', 'msg' => 'Class does not exist'], 422);
		}

		if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
		  return response()->json(['status' => 'error', 'msg' => "This is a demo user. You can't update or delete anything in this account. If you want to test all user features, sign up with a new account."], 422);
		}

		//$account = app()->make('account');
		$locale = request('locale', config('system.default_language'));
		app()->setLocale($locale);

		$settings = $model::getSettings()[auth()->user()->role];
		$permissions = $model::getPermissions()[auth()->user()->role];
		$actions = $model::getActions()[auth()->user()->role];
		$form = $model::getCreateForm()[auth()->user()->role];

		// Parse form settings
		$form = $this->parseFormSettings($form, $uuid === null);

		// Validation and values
		$validation = [];
		$values = [];

		if ($model == 'Platform\Models\Reward') {
		  $coupuns = [];
		}

		if ($model == 'Platform\Models\Campaign') {
		  $creditConversionRule = [];
		}

		foreach ($form as $tab) {
		  foreach ($tab['subs'] as $sub_key => $sub) {
			foreach ($sub['items'] as $i => $item) {
			  $column = ($item['type'] == 'relation' && $item['relation']['type'] == 'belongsToMany') ? $item['relation']['with'] : $item['column'] ?? null;
			  $request_column = (isset($item['column']) && strpos($item['column'], '->') !== false) ? str_replace('->', '___', $column) : $column;
			  $relation = ($item['type'] == 'relation') ? $item['relation'] : null;

			  if (isset($item['validate'])) {
				$item['validate'] = str_replace(['url: {require_protocol: false }', ': {require_protocol: true }'], '', $item['validate']);
				if (strpos($item['validate'], 'decimal') !== false) {
				  $item['validate'] = str_replace('decimal', 'numeric', $item['validate']);
				}
				if (strpos($item['validate'], 'unique:') !== false && $uuid !== null) {
				  // Get ID for record that will be updated to exclude it from unique validation
				  $query = $model::withoutGlobalScopes()->whereUuid($uuid)->first();
				  $item['validate'] = $item['validate'] . ',' . $query->id;
				}
				$validation[$request_column] = $item['validate'];
			  }

			  if (isset($item['validation'])) {
				$validation[$request_column] = $item['validation'];
			  }

			  $value = isset($request->formModel[$request_column]) ? $request->formModel[$request_column] : null;

			  if ($item['type'] == 'image') $value = isset($request->formModel[$column . '_media_file']) ? $request->formModel[$column . '_media_file'] : null;

			  if ($item['type'] == 'coupun_list') {
				$column = 'coupun';
			  }

			  if ($column !== null) {
				$values[$column] = [
				  'value' => $value,
				  'type' => $item['type'],
				  'relation' => $relation
				];
			  }
			}
		  }
		}

        $validate = Validator::make($request->formModel, $validation);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

		$query = new $model;

		// Check customer phone number
		$numbers = explode(" ", $values['customer_number']['value']);
		$isdCode = $numbers[0];
		$phoneNumber = '';
		foreach ($numbers as $key => $number) {
			if ($key != 0) {
				$phoneNumber .= $number;
			}
		}
		$customer_number = str_replace($isdCode, "",preg_replace('/\D+/', '', $phoneNumber));

		//Check file uploaded
		if (isset($request->formModel['avatar' . '_media_changed']) && $request->formModel['avatar' . '_media_changed']) {
			$file = $request->file('avatar');
			if ($file !== null) {
				$extension = $file->extension();
				/*
				* validation allowed images extentions
				*/
				if (! in_array($extension, self::allowedImageFileExtension)) {
					return response()->json([
                        'status' => 'error',
                        'msg' => 'File not allowed to upload.'
                    ], 422);
				}

				$query->addMedia($file)
					->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
					->toMediaCollection('avatar');
			} else {
				$query
					->clearMediaCollection('avatar');
			}
		}

		$query->campaign_id = $values['campaign_id']['value'];
		$query->name = $values['name']['value'];
		$query->email = $values['email']['value'];
		$query->country_code = $values['country_code']['value'];
		$query->country_isd_code = $values['country_isd_code']['value'];
		$query->customer_number = $customer_number;
		$query->card_number = $values['card_number']['value'];
		$query->password = bcrypt($values['password']['value']);
		$query->active = $values['active']['value'];

		$query->save();

		// Process after save
		foreach ($values as $column => $value) {
            if ($value['type'] == 'relation' && $value['relation']['type'] == 'belongsToMany') {
                $query->{$value['relation']['with']}()->sync($value['value']);
            }
		}

		return response()->json([
            'status' => 'success',
            'data' => auth()->user()->user_data
		]);
	}

    /**
     * Parse form settings
     *
     * @return array
     */
    public function parseFormSettings($form, $create = true) {
      foreach ($form as $tab_key => $tab) {
        foreach ($tab['subs'] as $sub_key => $sub) {
          foreach ($sub['items'] as $i => $item) {
            if (isset($item['column']) && strpos($item['column'], '->') !== false) {
              $new_column_name = str_replace('->', '___', $item['column']);
              //$form[$tab_key]['subs'][$sub_key]['items'][$i]['column'] = $new_column_name;
            }

            if ($create) {
              // Validate
              if (isset($item['validate_create'])) {
                $form[$tab_key]['subs'][$sub_key]['items'][$i]['validate'] = $item['validate_create'];
              }
              // Hint
              if (isset($item['hint_create'])) {
                $form[$tab_key]['subs'][$sub_key]['items'][$i]['hint'] = $item['hint_create'];
              }
              // Required
              if (isset($item['required_create'])) {
                $form[$tab_key]['subs'][$sub_key]['items'][$i]['required'] = $item['required_create'];
              }
            } else {
              // Validate
              if (isset($item['validate_edit'])) {
                $form[$tab_key]['subs'][$sub_key]['items'][$i]['validate'] = $item['validate_edit'];
              }
              // Hint
              if (isset($item['hint_edit'])) {
                $form[$tab_key]['subs'][$sub_key]['items'][$i]['hint'] = $item['hint_edit'];
              }
              // Required
              if (isset($item['required_edit'])) {
                $form[$tab_key]['subs'][$sub_key]['items'][$i]['required'] = $item['required_edit'];
              }
            }
          }
        }
      }
      return $form;
    }

	public function getCampaign(Request $request)
	{
		$campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

		return response()->json(['campaign_id' => $campaign->id]);
	}

	public function getPointDetails(Request $request)
	{
		$campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();
		$staff = Staff::withoutGlobalScopes()->where('id', Auth::user('staff')->id)->firstOrFail();

		$customer = Customer::select()
							->where('campaign_id', $campaign->id)
							->whereUuid(request('customer'),0)
							->with('history')
							->firstOrFail();

		return response()->json([
			'customer' => [
				'points' => $customer->points,
				'history' => $customer->getHistory()
			]
		], 200);
		// if($request->has('search') && $request->query('search') != '') {
		// 	$search = $request->query('search');
		// 	$timezone = $staff->getTimezone();
		// 	$customers = Customer::select('id','campaign_id','name','email','customer_number','card_number','last_login','uuid')
		// 						 ->where('campaign_id', $campaign->id)
		// 						 ->where('customer_number', 'like', "%{$search}%")
		// 						 ->get();
		// 	$customers = $customers->map(function ($record) use ($timezone) {
		// 		// $record->last_login = $record->last_login->timezone($timezone);

		// 		return collect($record)->only('name', 'email', 'customer_number', 'card_number', 'last_login','uuid');
		// 	});

		// 	return response()->json($customers);
		// } else {
		// 	return response()->json($campaign->getCustomers($campaign->id, $staff->getTimezone()));
		// }


		// // ---
		// if($request->has('uuid')) {
		// 	$customer = \App\Customer::where('uuid', $request->query('uuid'))->firstOrFail();
		// 	if(!$customer) {
		// 		return response()->json(['msg' => 'Invalid Customer UUID'], 422);
		// 	} else {
		// 		return response()->json([
		// 			'customer' => [
		// 				'points' => 101,
		// 				'history' => []
		// 			]
		// 		], 200);
		// 	}
		// } else {
		// 	return response()->json(['msg' => 'Missing Customer UUID'], 422);
		// }
	}
}
