<?php

namespace Deployer;

#set('composer_action', '');
#set('composer_options', 'install --optimize-autoloader --no-dev');

desc('Change app:permissions');
task('app:permissions', function () {
    writeln('change permissions to {{http_user}} in {{release_path}}');
    #run('cd {{deploy_path}}/shared && chown -Rf {{http_user}}:{{http_user}} storage');
   # run('cd {{release_path}} && chown -Rf {{http_user}}:{{http_user}} storage bootstrap && chown -Rf {{http_user}}:{{http_user}} public/*');
    run('cd {{release_path}} && php artisan autorun:commands');
}); 


// desc('Change Files And Folder Permissions');
// task('app:permissions', function () {
// 	run('find {{deploy_path}} -type d -exec chmod 0775 {} +');
// 	run('find {{deploy_path}} -type f -exec chmod 0644 {} +');
// });

// desc('Run "php artisan my-command" on the host.');
// task('artisan:autorun', function () {
//     cd('{{release_or_current_path}}');
//     run('php artisan autorun:commands');
// });