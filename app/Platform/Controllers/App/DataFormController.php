<?php

namespace Platform\Controllers\App;

use App\User;
use Carbon\Carbon;
use Platform\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Rules\TimeGreaterThan;
use App\Imports\MerchantImport;
use App\Imports\RewardImport;
use App\Jobs\DeactivateMerchantPlanFromAdmin;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessMerchantSendMail;
use App\Repositories\EmailTemplateRepository;
use Platform\Imports\CustomersImport;
use Illuminate\Support\Facades\Validator;
use App\Repositories\NotifPusherRepositories;
use Exception;
use Platform\Models\Campaign;

class DataFormController extends \App\Http\Controllers\Controller
{
    /*
    |--------------------------------------------------------------------------
    | DataForm Controller
    |--------------------------------------------------------------------------
    |
    | This controller is a blue print for the custom Vue DataForm component.
    |
    */

    const allowedImageFileExtension = ['png','jpg','jpeg'];

    private NotifPusherRepositories $notifPusher;
    private EmailTemplateRepository $emailTemplate;

    public function __construct(
        NotifPusherRepositories $notifPusher,
        EmailTemplateRepository $emailTemplate,
    ) {
        $this->notifPusher = $notifPusher;
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * This function generates the json response required for building the table form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDataForm(Request $request)
    {
        $model = $request->model;

        $uuid = $request->uuid;

        if (! class_exists($model)) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Class does not exist'
            ], 422);
        }

        //$account = app()->make('account');
        $locale = request('locale', config('system.default_locale'));

        app()->setLocale($locale);

		// $user = auth()->user();
		$user = User::withoutGlobalScopes()->find(auth()->id());

        $mode = $request->mode;

        $settings = $model::getSettings()[auth()->user()->role];

        $permissions = $model::getPermissions()[auth()->user()->role];

        $actions = $model::getActions()[auth()->user()->role];

        $extraSelectColumns = $model::getExtraSelectColumns()[auth()->user()->role];

        $extraQueryColumns = $model::getExtraQueryColumns()[auth()->user()->role];

        $translations = $model::getTranslations();

        // Transform translations
        $new_translations = [];

        foreach ($translations as $key => $translation) {
            $new_translations[$key] = $translation;

            $new_translations[$key . '_lowercase'] = strtolower($translation);
        }

        $translations = $new_translations;

        $limitationName = method_exists($model, 'getLimitationName') ? $model::getLimitationName() : '';

        if ($mode === 'crud') {
            if ($model == 'Platform\Models\Campaign') {
                // When creating / updating Websites, we need the Store Timezone
                $the_crud_campaign = $model::whereUuid($uuid)->first();

                if ($the_crud_campaign) {
                    $storeTimezone = $the_crud_campaign->business->timezone->timezone_name;
                } else {
                    $storeTimezone = 'UTC';

                }

                $form = $model::getCreateForm($storeTimezone)[auth()->user()->role];
            } else {
                $form = $model::getCreateForm()[auth()->user()->role];
            }
        } else {
            $form = $model::getImportForm()[$user->role];

            $relations = [];

            if ($model === 'App\Customer') {
                $relations = [
                    'campaign_id' => [
                        'column' => 'campaign_id',
                        'with' => 'campaign',
                        'type' => 'belongsTo',
                        'items' => Campaign::withoutGlobalScopes()->where('created_by', $user->id)->get()->map(fn($campaign) => [
                            'pk' => $campaign->id,
                            'val' => $campaign->name,
                        ])
                    ],
                ];
            }

            return response()->json([
                'status' => 'success',
                'settings' => $settings,
                'form' => $form,
                'relations' => $relations,
                'actions' => $actions,
                'translations' => $translations,
            ]);
        }

        // Get columns for select query
        $select = [];
        $relations = [];
        $dates = [];

      	if ($mode) {
			foreach ($form as $tab) {
				foreach ($tab['subs'] as $sub) {
					foreach ($sub['items'] as $item) {
						if (
							$item['type'] == 'button_reset_password' ||
							$item['type'] == 'coupun_list' ||
							$item['type'] == 'coupun_history' ||
							$item['type'] == 'credit_conversion_rule' ||
							$item['type'] == 'description' ||
							$item['type'] == 'image' ||
							$item['type'] == 'images' ||
							$item['type'] == 'reward_date_range' ||
							$item['type'] == 'static_paragraph' ||
							$item['type'] == 'label_only' ||
							($item['type'] == 'relation' && $item['relation']['type'] == 'belongsToMany')
						) {
							// Do nothing because these types have no columns
						} else {
							$select[] = $item['column'];
						}

						if ($item['type'] == 'date_time') {
							$dates[] = $item['column'];
						}

						// Relations
						if ($item['type'] == 'relation') {
							$relation = $item['relation'];

							$permission = (isset($relation['permission'])) ? $relation['permission'] : 'personal';

							$where = (isset($relation['where'])) ? $relation['where'] : '';

							if ($relation['type'] == 'hasOne' || $relation['type'] == 'belongsTo') {
								if ($permission == 'all') {
									$items = $model::with($relation['with'])
										->getRelation($relation['with'])
										->selectRaw($relation['pk'] . ' as pk, ' . $relation['val'] . ' as val')
										->where(function($query) use($where) {
											if ($where != '') {
												return $query->whereRaw($where);
											}
										})
										->orderBy($relation['orderBy'], $relation['order'])
										->get();
								} else {
									$items = $model::with($relation['with'])
										->getRelation($relation['with'])
										->selectRaw('' . $relation['pk'] . ' as pk, ' . $relation['val'] . ' as val')
										->where(function($query) use($where) {
											if ($where != '') return $query->whereRaw($where);
										})
										->where('created_by', $user->id)
										->orderBy($relation['orderBy'], $relation['order'])
										->get();
								}

								$items = $items->map(function ($item) use ($relation) {
									return ['pk' => $item->pk, 'val' => $item->val];
								});

								$items = $items->toArray();

								$relations[$item['column']] = [
									'column' => $item['column'],
									'with' => $relation['with'],
									'type' => $relation['type'],
									'items' => $items
								];
							}

							if ($relation['type'] == 'belongsToMany') {
								if ($permission == 'all') {
									$items = DB::table($relation['table'])
										->selectRaw($relation['pk'] . ' as pk, ' . $relation['val'] . ' as val')
										->where(function($query) use($where) {
											if ($where != '') return $query->whereRaw($where);
										})
										->orderBy($relation['orderBy'], $relation['order'])
										->get();
								} else {
									$items = DB::table($relation['table'])
										->selectRaw('' . $relation['pk'] . ' as pk, ' . $relation['val'] . ' as val')
										->where(function($query) use($where) {
											if ($where != '') return $query->whereRaw($where);
										})
										->where('created_by', $user->id)
										->orderBy($relation['orderBy'], $relation['order'])
										->get();
								}

								$items = $items->map(function ($item) use ($relation) {
									return ['pk' => $item->pk, 'val' => $item->val];
								});

								$items = $items->toArray();

								$relations[$relation['with']] = [
									'with' => $relation['with'],
									'type' => $relation['type'],
									'items' => $items
								];
							}
						}
					}
				}
			}
      	}


        // Defaults
        $values = [];

      	if ($uuid !== null) {
			// Query model
			$query = $model::select(array_merge($select, $extraSelectColumns, $extraQueryColumns));

			// Permission to view all records
			if ($permissions['view'] == 'all') {
				// Get result
				$values = clone $query->withoutGlobalScopes()
					->whereUuid($uuid)
					->first();

				//$allRecords = clone $query->withoutGlobalScopes()->get();
			}

			// Only records from account can be viewed
			if ($permissions['view'] == 'account') {
				$values = clone $query->whereUuid($uuid)->first();

				//$allRecords = clone $query->get();
			}

			// Only records created by user can be viewed
			if ($permissions['view'] == 'personal') {
				$values = clone $query->withoutGlobalScopes()
					->whereUuid($uuid)
					->where('created_by', $user->id)
					->first();

				//$allRecords = clone $query->where('created_by', auth()->user()->id)->get();
			}

			// Only records created by user can be viewed
			if (Str::startsWith($permissions['view'], 'created_by:')) {
				$created_by = explode(':', $permissions['delete']);

				$created_by = explode(',', $created_by[1]);

				$values = clone $query->withoutGlobalScopes()
					->whereUuid($uuid)
					->whereIn('created_by', $created_by)
					->first();

				//$allRecords = clone $query->whereIn('created_by', $created_by)->get();
			}
		} else {
			// Query model
			$query = $model::select(array_merge($select, $extraSelectColumns, $extraQueryColumns));

			// Permission to view all records
			if ($permissions['view'] == 'all') {
				// Get result
				$allRecords = clone $query->withoutGlobalScopes()->get();
			}

			// Only records from account can be viewed
			if ($permissions['view'] == 'account') {
				$allRecords = clone $query->get();
			}

			// Only records created by user can be viewed
			if ($permissions['view'] == 'personal') {
				$allRecords = clone $query->withoutGlobalScopes()
					->where('created_by', $user->id)
					->get();
			}

			// Only records created by user can be viewed
			if (Str::startsWith($permissions['view'], 'created_by:')) {
				$created_by = explode(':', $permissions['delete']);

				$created_by = explode(',', $created_by[1]);

				$allRecords = clone $query->whereIn('created_by', $created_by)->get();
			}
		}

		if (empty($values)) {
			foreach ($form as $tab) {
				foreach ($tab['subs'] as $sub) {
					foreach ($sub['items'] as $item) {
						if ($item['type'] == 'description' || $item['type'] == 'button_reset_password') {
							// Do nothing
						} elseif ($item['type'] == 'relation' && ($item['relation']['type'] == 'hasOne' || $item['relation']['type'] == 'belongsTo')) {
							$values[$item['column'] . '_loading'] = false;

							$values[$item['column'] . '_search'] = null;

							$values[$item['column'] . '_items'] = [];
						} elseif ($item['type'] == 'relation' && $item['relation']['type'] == 'belongsToMany') {
							// No column
							$values[$item['relation']['with'] . '_items'] = [];
						} else {
							$column = (strpos($item['column'], '->') !== false) ? str_replace('->', '___', $item['column']) : $item['column'];

							$values[$column] = (isset($item['default'])) ? $item['default'] : '';
						}
					}
				}
			}
		}

      	// Parse additional settings
		foreach ($form as $tab_key => $tab) {
			foreach ($tab['subs'] as $sub_key => $sub) {
				foreach ($sub['items'] as $i => $item) {
					$new_column_name = isset($item['column']) ? $item['column'] : null;
					// Parse JSON columns
					if (isset($item['column']) && strpos($item['column'], '->') !== false) {
						$columns = explode('->', $item['column']);

						$column = 'json_unquote(json_extract(`' . $columns[0] . '`, \'$."' . $columns[1] . '"\'))';

						//$column = $columns[0] . '->' . $columns[1];
						$new_column_name = str_replace('->', '___', $item['column']);

						//$value = (isset($values->{$column})) ? $values->{$column} : isset($values[$new_column_name]) ? $values[$new_column_name] : null;
						$value = (isset($values->{$column})) ? $values->{$column} : $values[$new_column_name] ?? null;

						switch ($value) {
							case 'null':
							case 'true':
							case 'false':
								$value = json_decode($value);
								break;
						}

						$values[$new_column_name] = $value;

						$form[$tab_key]['subs'][$sub_key]['items'][$i]['column'] = $new_column_name;

						// Remove unparsed column
						unset($values->{$column});
					}

					if ($item['type'] == 'currency') {
						$fraction_digits = $user->getCurrencyFormat('fraction_digits');

						$multiplier = intval(str_pad(1, $fraction_digits + 1, 0));

						$values[$new_column_name] = (is_numeric($values[$new_column_name])) ? $values[$new_column_name] / $multiplier : $values[$new_column_name];
					} elseif ($item['type'] == 'image') {
						// Defaults
						$values[$new_column_name . '_media_name'] = '';

						$values[$new_column_name . '_media_url'] = '';

						$values[$new_column_name . '_media_file'] = '';

						$values[$new_column_name . '_media_changed'] = false;

						// Check if media is attached
						if ($uuid !== null) {
							$media = $values->getFirstMedia($new_column_name);

							if ($media !== null) {
								$values[$new_column_name . '_media_name'] = $media->name;

								$values[$new_column_name . '_media_url'] = $media->getFullUrl();
							}
						}
					} elseif ($item['type'] == 'images') {
						// Defaults
						$values[$new_column_name . '_media_name'] = '';

						$values[$new_column_name . '_media_url'] = '';

						$values[$new_column_name . '_media_file'] = '';

						$values[$new_column_name . '_media_changed'] = false;

						// Check if media is attached
						if ($uuid !== null) {
							// $media = $values->getFirstMedia($new_column_name);

							// if ($media !== null) {
							// 	$values[$new_column_name . '_media_name'] = $media->name;

							// 	$values[$new_column_name . '_media_url'] = $media->getFullUrl();
							// }
						}
					} elseif ($item['type'] == 'relation' && $item['relation']['type'] == 'belongsToMany') {
						if ($uuid !== null) {
							$values[$item['relation']['with']] = $values->{$item['relation']['with']}()->pluck($relation['remote_pk']);
						}
					} elseif ($item['type'] == 'password') {
						$values[$new_column_name . '_show'] = false;
					} elseif ($item['type'] == 'boolean') {
						$values[$new_column_name] = (boolean) $values[$new_column_name];
					} elseif ($item['type'] == 'date_time') {
						if ($values[$new_column_name] != '') {
							$value = Carbon::parse($values[$new_column_name], config('app.timezone'))
								->setTimezone($user->getTimezone());

							$values[$new_column_name] = $value->format('Y-m-d H:i:s');
						} else {
							$values[$new_column_name] = null;
						}
					} else if ($item['type'] == 'coupun_list'){
						if (isset($values->id)) {
							$coupun = $model::where('id', $values->id)->first();

							$values['coupun_list'] = $coupun->coupunCode;
						} else {
							$values['coupun_list'] = [];
						}
					} else if ($item['type'] == 'coupun_history'){
						if (isset($values->id)) {
							$coupun = $model::where('id', $values->id)
								->with(['coupunUsed.users', 'coupunUsed.coupunCode'])
								->first();

							$values['coupun_history'] = $coupun->coupunUsed;
						} else {
							$values['coupun_history'] = [];
						}
					} else if($item['type'] == 'credit_conversion_rule'){
						if (isset($values->id)) {
							$campaign = $model::where('id', $values->id)->with(['creditRuleConversion'])->first();

							$values['credit_conversion_rule'] = $campaign->creditRuleConversion;
						} else {
							$values['credit_conversion_rule'] = [];
						}
					}
				}
			}
		}

		// Parse form settings
		$form = $this->parseFormSettings($form, $uuid === null);

		// Remove unused columns
		$values = collect($values)->except($extraQueryColumns);

		// Limitations
		$limitReached = false;

		$limitationMax = -1;

		$count = 0;

		if ($limitationName != '' && $uuid === null) {
			$count = $allRecords->count();

			$limitationMax = $user->plan_limitations[$limitationName];

			$limitReached = ($count < $limitationMax) ? false : true;
		}

		return response()->json([
			'status' => 'success',
			'settings' => $settings,
			'form' => $form,
			'relations' => $relations,
			'values' => $values,
			'dates' => $dates,
			'actions' => $actions,
			'translations' => $translations,
			'count' => $count,
			'max' => $limitationMax,
			'limitReached' => $limitReached
		]);
	}

    /**
     * Get relationship data
     *
     * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function postGetRelation(Request $request)
	{
      	$model = $request->model;

		$uuid = $request->uuid;

		$relation = $request->relation;

		if (! class_exists($model)) {
			return response()->json([
				'status' => 'error',
				'msg' => 'Class does not exist'
			]);
		}

		$locale = request('locale', config('system.default_language'));

		app()->setLocale($locale);

		$fields = [];

		if ($relation['type'] == 'hasOne') {
			$relation['pk'] = ($relation['pk'] == 'uuid') ? "LOWER(CONCAT(
				SUBSTR(HEX(uuid), 1, 8), '-',
				SUBSTR(HEX(uuid), 9, 4), '-',
				SUBSTR(HEX(uuid), 13, 4), '-',
				SUBSTR(HEX(uuid), 17, 4), '-',
				SUBSTR(HEX(uuid), 21)
			))" : $relation['pk'];

			$fields = $model::with($relation['with'])
				->getRelation($relation['with'])
				->selectRaw($relation['pk'] . ' as pk, ' . $relation['val'] . ' as val')
				->orderBy($relation['orderBy'], $relation['order'])
				->get()
				->toArray();
      	}

      	return response()->json([
			'status' => 'success',
			'fields' => $fields
		]);
    }

    /**
     * Parse form settings
     *
     * @return array
     */
    public function parseFormSettings($form, $create = true)
    {
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

    /**
     * Save post
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postSaveRecord(Request $request)
    {
        $model = $request->model;

        $uuid = ($request->uuid == 'null') ? null: $request->uuid;

        $request->formModel = (array) json_decode($request->formModel);

        if ($request->uuid == null) {
            $user = auth()->user();
                if($user->role != 1) {

                $checkingPlanLimitation = Plan::checkingUserPlanLimitations(
                    auth()->user(), $model
                );

                if ($checkingPlanLimitation['error']) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => $checkingPlanLimitation['message']
                    ], 422);
                }
            }
        }

        if (! class_exists($model)) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Class does not exist'
            ], 422);
        }

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
            return response()->json([
                'status' => 'error',
                'msg' => "This is a demo user. You can't update or delete anything in this account. If you want to test all user features, sign up with a new account."
            ], 422);
        }

        //$account = app()->make('account');
        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        // $user = auth()->user();
        $user = User::withoutGlobalScopes()->find(auth()->id());

        // $settings = $model::getSettings()[$user->role];

        $permissions = $model::getPermissions()[$user->role];

        // $actions = $model::getActions()[$user->role];

        $form = $model::getCreateForm()[$user->role];

        // Parse form settings
        $form = $this->parseFormSettings($form, $uuid === null);

        // Validation and values
        $validation = [];

        $values = [];

        $requestForm = [];

        if ($model == 'Platform\Models\Reward') {
            $coupuns = [];
        }

        if ($model == 'Platform\Models\Campaign') {
            $creditConversionRule = [];
        }

        foreach ($form as $tab) {
            foreach ($tab['subs'] as $sub_key => $sub) {
                foreach ($sub['items'] as $i => $item) {
                    if (
                        $item['type'] == 'reward_date_range' ||
                        $item['type'] == 'static_paragraph' ||
                        $item['type'] == 'label_only'
                    ) {
                        continue;
                    }

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

                        // Check if there is any `time_greated_than` validation rule
                        $validation_items = explode('|', $item['validate']);
                        $item_validate = [];
                        $is_there_time_greater_than = false;
                        foreach($validation_items as $current_rule) {
                            if($current_rule == 'time_greater_than:' . str_replace('to','from',$request_column)) {
                                $is_there_time_greater_than = true;
                                $time_from_value = isset($request->formModel[str_replace('to','from',$request_column)]) ? $request->formModel[str_replace('to','from',$request_column)] : '00:00';
                                $item_validate[] = new TimeGreaterThan($time_from_value);
                            } else {
                                $item_validate[] = $current_rule;
                            }
                        }

                        if ($is_there_time_greater_than) {
                            $validation[$request_column] = $item_validate;
                        } else {
                            $validation[$request_column] = $item['validate'];
                        }
                    }

                    if (isset($item['validation'])) {
                        $validation[$request_column] = $item['validation'];
                    }

                    $value = isset($request->formModel[$request_column]) ? $request->formModel[$request_column] : null;

                    if ($item['type'] == 'image') {
                        $value = isset($request->formModel[$column . '_media_file']) ? $request->formModel[$column . '_media_file'] : null;
                    }

                    if ($item['type'] == 'coupun_list') {
                        $column = 'coupun';
                    }

                    if ($column !== null) {
                        $requestForm[$column] = $value;

                        if ($item['type'] === 'image') {
                            $requestForm[$column] = $request->file($column);
                        }

                        $values[$column] = [
                            'value' => $value,
                            'type' => $item['type'],
                            'relation' => $relation
                        ];
                    }
                }
            }
        }

        $validate = Validator::make($requestForm, $validation);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        if ($uuid === null) { // Insert
            if ($permissions['create']) {
                $query = new $model;
            }
        } else { // Update
            $query = false;

            // All records can be updated
            if ($permissions['update'] == 'all') {
                $query = $model::withoutGlobalScopes()->whereUuid($uuid)->first();
            }

            // Only records from account can be updated
            if ($permissions['update'] == 'account') {
                $query = $model::whereUuid($uuid)->first();
            }

            // Only records created by user can be updated
            if ($permissions['update'] == 'personal') {
                $query = $model::withoutGlobalScopes()->whereUuid($uuid)->where('created_by', $user->id)->first();
            }

            // Only records created by user can be updated
            if (Str::startsWith($permissions['update'], 'created_by:')) {
                $created_by = explode(':', $permissions['delete']);

                $created_by = explode(',', $created_by[1]);

                $query = $model::withoutGlobalScopes()
                    ->whereUuid($uuid)
                    ->whereIn('created_by', $created_by)
                    ->first();
            }

            if ($query === false) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'No permission to edit record'
                ], 422);
            }
        }

        foreach ($values as $column => $value) {
            if ($value['type'] == 'image') { // Process image upload
                // Check for changes
                if (isset($request->formModel[$column . '_media_changed']) && $request->formModel[$column . '_media_changed']) {
                    $file = $request->file($column);

                    if ($file !== null) {
                        $extension = $file->extension();
                        /*
                        * validation allowed images extentions
                        */
                        if (! in_array($extension, self::allowedImageFileExtension)) {
                            return response()->json(['status' => 'error', 'msg' => 'File not allowed to upload.'], 422);
                        }

                        $query
                            ->addMedia($file)
                            ->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                            ->toMediaCollection($column);
                    } else {
                        $query->clearMediaCollection($column);
                    }
                }
            } elseif($value['type'] == 'images') { // Process multi image upload

            } elseif ($value['type'] == 'currency') {
                $multiplier = str_pad(1, $user->getCurrencyFormat('fraction_digits') + 1, 0);

                $query->{$column} = (is_numeric($value['value'])) ? $value['value'] * $multiplier : $value['value'];
            } elseif ($value['type'] == 'boolean') {
                $query->{$column} = ($value['value'] == '' || $value['value'] == null) ? 0 : 1;
            } elseif ($value['type'] == 'password') {
                if ($value['value'] != '') $query->{$column} = bcrypt($value['value']);
            } elseif ($value['type'] == 'date_time') {
                if ($value['value'] != '') {
                    $value = Carbon::parse($value['value'], $user->getTimezone())->setTimezone(config('app.timezone'));

                    $query->{$column} = $value->format('Y-m-d H:i:s');
                }
            } elseif ($value['type'] == 'relation' && $value['relation']['type'] == 'belongsToMany' || $value['type'] == 'coupun_history') {
                // Do nothing, sync afterwards because it's possible a new record without id yet
            } elseif ($value['type'] == 'coupun_list') {
                $coupuns = $value['value'];
            } elseif ($value['type'] == 'credit_conversion_rule') {
                $conversion_mode = $request->formModel['credit_points_mode'] ?? 'range';

                $creditConversionRule = $value['value'];

                $rules = array_filter($creditConversionRule, fn($item) => $item->mode === $conversion_mode);

                $comparisons = $rules;

                foreach ($rules as $rule) {
                    if ($conversion_mode === 'range') {
                        $min_amount = (int) $rule->min_amount;

                        $max_amount = (int) $rule->max_amount;

                        $rate = (int) $rule->rate;

                        $uuid = $rule->uuid;

                        if ($min_amount < 0 || $max_amount < 0 || $rate < 0) {
                            return response()->json([
                                'status' => 'error',
                                'errors' => [
                                    'credit_conversion_rule' => [
                                        [
                                            'uuid' => $uuid,
                                            'msg' => 'Please fill with valid number.',
                                        ],
                                    ],
                                ],
                            ], 422);
                        }

                        if ($min_amount >= 0 && $max_amount >= 0 && $min_amount === $max_amount) {
                            return response()->json([
                                'status' => 'error',
                                'errors' => [
                                    'credit_conversion_rule' => [
                                        [
                                            'uuid' => $uuid,
                                            'msg' => 'Make sure amount range not same.',
                                        ],
                                    ],
                                ],
                            ], 422);
                        }

                        if ($min_amount > $max_amount || $max_amount < $min_amount) {
                            return response()->json([
                                'status' => 'error',
                                'errors' => [
                                    'credit_conversion_rule' => [
                                        [
                                            'uuid' => $uuid,
                                            'msg' => 'The minimum amount must be less than maximum amount.',
                                        ],
                                    ],
                                ],
                            ], 422);
                        }

                        foreach ($comparisons as $comparison) {
                            if ($comparison->min_amount < $comparison->max_amount && $uuid !== $comparison->uuid) {
                                if (in_array($min_amount, range($comparison->min_amount, $comparison->max_amount))) {
                                    return response()->json([
                                        'status' => 'error',
                                        'errors' => [
                                            'credit_conversion_rule' => [
                                                [
                                                    'uuid' => $comparison->uuid,
                                                    'msg' => 'Rule amount already added.',
                                                ],
                                            ],
                                        ],
                                    ], 422);
                                } else if (in_array($max_amount, range($comparison->min_amount, $comparison->max_amount))) {
                                    return response()->json([
                                        'status' => 'error',
                                        'errors' => [
                                            'credit_conversion_rule' => [
                                                [
                                                    'uuid' => $comparison->uuid,
                                                    'msg' => 'Please increase the minimum amount.',
                                                ],
                                            ],
                                        ],
                                    ], 422);
                                }
                            }
                        }
                    } else if ($conversion_mode === 'step') {
                        $min_amount = (int) $rule->min_amount;

                        $stepping_mode = $rule->stepping_mode;

                        $step_amount = (int) $rule->step_amount;

                        $uuid = $rule->uuid;

                        if ($stepping_mode && $step_amount > $min_amount) {
                            return response()->json([
                                'status' => 'error',
                                'errors' => [
                                    'credit_conversion_rule' => [
                                        [
                                            'uuid' => $uuid,
                                            'msg' => 'The step amount must be less than or equal minimum amount.',
                                        ],
                                    ],
                                ],
                            ], 422);
                        }

                        foreach ($comparisons as $comparison) {
                            if ($min_amount === $comparison->min_amount && $uuid !== $comparison->uuid) {
                                return response()->json([
                                    'status' => 'error',
                                    'errors' => [
                                        'credit_conversion_rule' => [
                                            [
                                                'uuid' => $comparison->uuid,
                                                'msg' => 'Rule amount already added.',
                                            ],
                                        ],
                                    ],
                                ], 422);
                            }
                        }
                    }
                }

                $query->credit_points_mode = $conversion_mode;
            } elseif ($value['type'] == 'phone') {
                $numbers = explode(" ", $value['value']);

                $isdCode = $numbers[0];

                $phoneNumber = '';

                foreach ($numbers as $key => $number) {
                    if ($key != 0) {
                        $phoneNumber .= $number;
                    }
                }

                $value['value'] = str_replace($isdCode, "", preg_replace('/\D+/', '', $phoneNumber));

                $query->{$column} = $value['value'];
            } else {
                $query->{$column} = ($value['value'] == '' && $value['value'] != '0') ? null : $value['value'];
            }
        }

        if ($model == 'App\User') {
            $query->currency_code = config('system.default_currency');
        }

        if ($model == 'App\User' || $model == 'App\Customer') {
            $query->verification_code = Str::random(32);
            $query->language = config('system.default_language');
            $query->locale = config('system.default_locale');
            $query->timezone = config('system.default_timezone');
        }

        if ($model == 'Platform\Models\Reward') {
            if ($model == 'Platform\Models\Reward' && isset($query->id)) {
                \Platform\Models\CoupunCode::where('reward_id', $query->id)
                    ->where('status', 0)
                    ->delete();
            }
        }

        if ($model == 'Platform\Models\Campaign') {
            if ($model == 'Platform\Models\Campaign' && isset($query->id)) {
                \Platform\Models\CreditRuleConversion::where('campaign_id', $query->id)
                    ->delete();
            }
        }

        $query->save();

        if ($model == 'Platform\Models\Reward') {
            if (count($coupuns) > 0) {
                foreach ($coupuns as $coupun) {
                    if ($coupun->status == 0) {
                        $coupunModel = new \Platform\Models\CoupunCode;
                        $coupunModel->name = $coupun->name;
                        $coupunModel->code = $coupun->code;
                        $coupunModel->status = $coupun->status;
                        $coupunModel->reward_id = $query->id;
                        $coupunModel->save();
                    }
                }
            }
        }

        if ($model == 'Platform\Models\Campaign') {
            if (count($creditConversionRule) > 0) {
                foreach ($creditConversionRule as $rule) {
                    $creditConversionRuleModel = new \Platform\Models\CreditRuleConversion;
                    $creditConversionRuleModel->max_amount = $rule->max_amount;
                    $creditConversionRuleModel->min_amount = $rule->min_amount;
                    $creditConversionRuleModel->rate = $rule->rate;
                    $creditConversionRuleModel->type = $rule->type;
                    $creditConversionRuleModel->stepping_mode = $rule->stepping_mode;
                    $creditConversionRuleModel->step_amount = $rule->step_amount;
                    $creditConversionRuleModel->mode = $rule->mode;
                    $creditConversionRuleModel->campaign_id = $query->id;
                    $creditConversionRuleModel->save();
                }
            }
        }

        // Process after save
        foreach ($values as $column => $value) {
            if ($value['type'] == 'relation' && $value['relation']['type'] == 'belongsToMany') {
                $query->{$value['relation']['with']}()->sync($value['value']);
                if($model == 'Platform\Models\Campaign' && $value['relation']['with'] == 'rewards') {
                    // If writing to campaign_reward pivot table, also copy default date range from rewards
                    $the_campaign_id = $query->id;
                    foreach($value['value'] as $the_reward_id) {
                        $the_reward = \Platform\Models\Reward::findOrFail($the_reward_id);
                        \Platform\Models\CampaignReward::where('campaign_id', $the_campaign_id)
                            ->where('reward_id', $the_reward_id)
                            ->update([
                                'active_from' => $the_reward->active_from,
                                'expires_at' => $the_reward->expires_at,
                                'active_monday' => $the_reward->active_monday,
                                'active_monday_from' => $the_reward->active_monday_from,
                                'active_monday_to' => $the_reward->active_monday_to,
                                'active_tuesday' => $the_reward->active_tuesday,
                                'active_tuesday_from' => $the_reward->active_tuesday_from,
                                'active_tuesday_to' => $the_reward->active_tuesday_to,
                                'active_wednesday' => $the_reward->active_wednesday,
                                'active_wednesday_from' => $the_reward->active_wednesday_from,
                                'active_wednesday_to' => $the_reward->active_wednesday_to,
                                'active_thursday' => $the_reward->active_thursday,
                                'active_thursday_from' => $the_reward->active_thursday_from,
                                'active_thursday_to' => $the_reward->active_thursday_to,
                                'active_friday' => $the_reward->active_friday,
                                'active_friday_from' => $the_reward->active_friday_from,
                                'active_friday_to' => $the_reward->active_friday_to,
                                'active_saturday' => $the_reward->active_saturday,
                                'active_saturday_from' => $the_reward->active_saturday_from,
                                'active_saturday_to' => $the_reward->active_saturday_to,
                                'active_sunday' => $the_reward->active_sunday,
                                'active_sunday_from' => $the_reward->active_sunday_from,
                                'active_sunday_to' => $the_reward->active_sunday_to
                            ]);
                    }
                }
            }
        }

        if ($model == 'App\User') {
            EmailTemplate::insertRecord($query->id);

            if ($query->plan_id) {
                DeactivateMerchantPlanFromAdmin::dispatch($query)
                    ->delay($query->expires)
                    ->onQueue('billings');
            }
        }

        if ($model == 'App\Staff') {
            $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
                ->where('business_id', $request->formModel['businesses'][0])
                ->first();

            if ($campaign) {
                $emailTemplate = EmailTemplate::where('name', 'staff_registeration')->where('created_by', auth()->user()->id)->first();

                $login_url = $campaign->url . '/login';

                $cta_button = '<a href="'.$login_url.'" class="button button-primary" target="_blank">Login</a>';

                $variableTemplate = [
                    '{{ website_name }}',
                    '{{ website_url }}',
                    '{{ login_button }}',
                    '{{ login_url }}',
                    '{{ name_of_user }}',
                    '{{ email_of_user }}'
                ];

                $variableChange = [
                    $campaign->name,
                    $campaign->url,
                    $cta_button,
                    $login_url,
                    $request->formModel['name'],
                    $request->formModel['email']
                ];

                $email = new \stdClass;

                $account = app()->make('account');

                $email->website_name = $campaign->name;
                $email->website_url = $campaign->url;
                $email->from_name = $account->app_mail_name_from;
                $email->from_email = $account->app_mail_address_from;

                $email->to_name = $request->formModel['name'];
                $email->to_email = $request->formModel['email'];
                $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
                $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

                // Mail::send(new \App\Mail\TemplateMail($email));
                ProcessMerchantSendMail::dispatch(
                    $email,
                    $campaign->smtp_service_id
                )->onQueue('emails');
            }
        }

        if ($model == 'Platform\Models\Reward') {
            \Platform\Models\CampaignReward::where('reward_id', $query->id)
                ->update([
                    'active_from' => $query->active_from,
                    'expires_at' => $query->expires_at,
                    'active_monday' => $query->active_monday,
                    'active_monday_from' => $query->active_monday_from,
                    'active_monday_to' => $query->active_monday_to,
                    'active_tuesday' => $query->active_tuesday,
                    'active_tuesday_from' => $query->active_tuesday_from,
                    'active_tuesday_to' => $query->active_tuesday_to,
                    'active_wednesday' => $query->active_wednesday,
                    'active_wednesday_from' => $query->active_wednesday_from,
                    'active_wednesday_to' => $query->active_wednesday_to,
                    'active_thursday' => $query->active_thursday,
                    'active_thursday_from' => $query->active_thursday_from,
                    'active_thursday_to' => $query->active_thursday_to,
                    'active_friday' => $query->active_friday,
                    'active_friday_from' => $query->active_friday_from,
                    'active_friday_to' => $query->active_friday_to,
                    'active_saturday' => $query->active_saturday,
                    'active_saturday_from' => $query->active_saturday_from,
                    'active_saturday_to' => $query->active_saturday_to,
                    'active_sunday' => $query->active_sunday,
                    'active_sunday_from' => $query->active_sunday_from,
                    'active_sunday_to' => $query->active_sunday_to
                ]);
        }


        return response()->json([
            'status' => 'success',
            'data' => $user->user_data
        ]);
      // return response()->json(['status' => 'success'], 200);
    }

    /**
     * Handle importing bulk customers data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function processImport(Request $request)
    {
        $this->validate($request, [
            'model' => 'required',
            'file' => 'required|mimes:csv,xls,xlsx',
        ]);

        $file = $request->file('file');

        $formModel = json_decode($request->formModel, TRUE);

        if ($request->model === "App\\User") {
            Excel::import(new MerchantImport($this->notifPusher), $file);
        } else if ($request->model === "App\\Customer") {
            Excel::import(new CustomersImport($formModel['campaign_id'], $this->emailTemplate), $file);
        } else if ($request->model === "Platform\\Models\\Reward") {
            Excel::import(new RewardImport(), $file);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}
