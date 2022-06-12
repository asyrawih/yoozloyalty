<?php

namespace Deployer;
desc('Copy updated storage and public media files to new release');
task('files:update_code', function () {
    writeln("<info>Copy updated storage and public media files to new release</info>");
   // upload('./storage', '{{deploy_path}}/shared');
   $sharedPath = "{{deploy_path}}/shared";
   run("cp -ru {{release_or_current_path}}/storage/. {$sharedPath}/storage");
   run("echo {{release_or_current_path}} && cp -ru {{current_path}}/public/media/. {{release_or_current_path}}/public/media");
});