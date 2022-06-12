<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/rsync.php';
require 'recipe/permissiontasks.php';
require 'recipe/copystorage_publicmedia.php';
//set('keep_releases', 5);
//set('bin/composer', 'composer');
//set('bin/php', '/www/server/php/80/bin/php');
//set('release_name', function () {
 //   return (string) run('date +"%Y-%m-%d_%H-%M-%S"');
//});

// Config

set('application', 'loyalty-system');
#set('deploy_path', '~/{{application}}');
set('repository', 'git@github.com:setspl/loyalty-system.git');
set('ssh_multiplexing', true); // Speed up deployment

set('rsync_src', function () {
    return __DIR__; // If your project isn't in the root, you'll need to change this.
});

#set('rsync_dest', '{{deploy_path}}/shared');

// Files you don't want in your production server.
add('rsync', [
    'exclude' => [
        '.git',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

add('rsync', [
  'flags' => 'rzlE'
]);

task('php-fpm:restart', function () {
    run('service php8.0-fpm restart');
});

task('deploy:update_code', function () {
    writeln("<info>Skipping git Uploading files to server</info>");
   // upload('./storage', '{{deploy_path}}/shared');
   //$sharedPath = "{{deploy_path}}/shared";
   //run("cp -ru {{release_path}}/storage/ {$sharedPath}/storage");
   //run("cp -ru {{current_path}}/public/media/ {{release_path}}/public/media");
});

// task('deploy:check_env', function () {
//    writeln("<info>Update check env</info>");
//    #upload('./env.example', '{{release_path}}/shared');
//    //$sharedPath = "{{deploy_path}}/shared";
//    //run("cp -ru {{release_path}}/storage/ {$sharedPath}/storage");
//    //run("cp -ru {{current_path}}/public/media/ {{release_path}}/public/media");
// });


// Hosts

host('develop')
->setRemoteUser("centos") // SSH user
->setHostName("ci-cd.yoozloyalty.net")
->setDeployPath('/www/wwwroot/ci-cd.yoozloyalty.net');  // Deploy path
add('shared_files', [".env"]);
add('shared_dirs', ["uploads", "storage",]);
add('writable_dirs', ["bootstrap/cache", "storage", "storage/app", "storage/framework", "storage/logs", "public"]);

host('staging')
->setRemoteUser("centos") // SSH user
->setHostName("yoozloyalty.net")
->setDeployPath('/www/wwwroot/staging_yoozloyalty.net');  // Deploy path
add('shared_files', [".env"]);
add('shared_dirs', ["uploads", "storage"]);
add('writable_dirs', ["bootstrap/cache", "storage", "storage/app", "storage/framework", "storage/logs", "public"]);

host('nearbyreward')
->setRemoteUser("centos") // SSH user
->setHostName("nearbyreward.com")
->setDeployPath('/www/wwwroot/nearbyreward.com.ci-cd');  // Deploy path
add('shared_files', [".env"]);
add('shared_dirs', ["uploads", "storage"]);
add('writable_dirs', ["bootstrap/cache", "storage", "storage/app", "storage/framework", "storage/logs", "public"]);

host('production')
->setRemoteUser("centos") // SSH user
->setHostName("yoozloyalty.com")
->setDeployPath('/www/wwwroot/production_yoozloyalty.com');  // Deploy path
add('shared_files', [".env"]);
add('shared_dirs', ["uploads", "storage"]);
add('writable_dirs', ["bootstrap/cache", "storage", "storage/app", "storage/framework", "storage/logs", "public"]);

desc('Deploy the application');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:release',
    'rsync',
    'files:update_code',
    'deploy:shared',
    'deploy:writable',
    'artisan:view:cache',   
    'artisan:config:cache',   
    'artisan:migrate', 
    'app:permissions',   
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup',
]);

after('deploy:failed', 'deploy:unlock'); // In case your deployment goes wrong