<?php
error_reporting(0);
if ($_POST['submit']) {
    if ($_POST['plugin-name']) {
        $pluginName = str_replace(" ", "-", strtolower($_POST['plugin-name']));
        $root = "/var/lib/openshift/52d9fad8500446d295000294/app-root/data/WordPress-Plugin-Boilerplate-master/";
        $originalDir = "plugin-name";
        $newDir = time() . $pluginName;
        $newAbsDir = $root . $newDir;
        shell_exec("mkdir {$newAbsDir}");
        shell_exec("cp -r {$root}/plugin-name {$newAbsDir}");
        shell_exec("chmod -R 0777 {$newAbsDir}");

        foreach (glob_recursive( "{$newAbsDir}/plugin-name/*.php") as $phpfile) {
            $filedata = file_get_contents($phpfile);
            $newdata = str_replace("Plugin_Name", str_replace(" ","_",$_POST['plugin-name']), $filedata);
            $newdata = str_replace(" * @author    Your Name <email@example.com>", " * @author    {$_POST['author-name']} <{$_POST['author-email']}>", $newdata);
            $newdata = str_replace(" * @link      http://example.com", " * @link      {$_POST['author-uri']}", $newdata);
            $newdata = str_replace(" * @copyright 2014 Your Name or Company Name", " * @copyright 2014 {$_POST['author-name']}", $newdata);

            $newdata = str_replace("plugin-name", $pluginName, $newdata);
            file_put_contents($phpfile, $newdata);
        }


        rename( "{$newAbsDir}/plugin-name/plugin-name.php", "{$newAbsDir}/plugin-name/{$pluginName}.php");
        rename( "{$newAbsDir}/plugin-name/admin/class-plugin-name-admin.php", "{$newAbsDir}/plugin-name/admin/class-{$pluginName}-admin.php");
        rename( "{$newAbsDir}/plugin-name/public/class-plugin-name.php",  "{$newAbsDir}/plugin-name/public/class-{$pluginName}.php");

        $plugindetails = file_get_contents("{$newAbsDir}/plugin-name/{$pluginName}.php");
        $newdata = str_replace(" * Plugin Name:       @TODO", " * Plugin Name:       {$_POST['plugin-name']}", $plugindetails);
        $newdata = str_replace(" * Plugin URI:        @TODO", " * Plugin URI:       {$_POST['plugin-uri']}", $newdata);
        $newdata = str_replace(" * Description:       @TODO", " * Description:       {$_POST['plugin-description']}", $newdata);
        $newdata = str_replace(" * Author:            @TODO", " * Author:       {$_POST['author-name']}", $newdata);
        $newdata = str_replace(" * Author URI:        @TODO", " * Author URI:       {$_POST['author-uri']}", $newdata);
        $newdata = str_replace(" * @author    Your Name <email@example.com>", "* @author    {$_POST['author-name']} <{$_POST['author-email']}>", $newdata);
        $newdata = str_replace(" * Text Domain:       {$pluginName}-locale", " * Text Domain:       {$pluginName}", $newdata);
        file_put_contents( "{$newAbsDir}/plugin-name/{$pluginName}.php", $newdata);
        shell_exec("mv {$newAbsDir}/plugin-name {$newAbsDir}/{$pluginName}");

        $tarBall = "{$newAbsDir}/{$pluginName}.tar.gz";

        shell_exec("cd {$newAbsDir} && tar -zcvf {$tarBall} {$pluginName}");

        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename=' . "{$pluginName}.tar.gz");
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($tarBall));
        ob_end_clean();
        readfile($tarBall);
        shell_exec("rm -fr {$newAbsDir}");

        die();
    }
}

function glob_recursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }

    return $files;
}

?>
<html>
<head>
    <title>Personalized Plugin Generator from Tom Mcfarlin's Plugin Boilerplate</title>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css"/>
    <style type="text/css">
        .jumbotron {
            margin-top: 40px;
        }

        .jumbotron h1 {
            font-size: 36px;
            margin-bottom: 40px;
        }

        .jumbotron p {
            font-size: 18px;
            line-height: 32px;
        }


    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="jumbotron">
                <h1>Plugin Boilerplate Generator</h1>

                <p>
                    This is a plugin boilerplate code generator from Tom Mcfarlin's <a target="_blank"
                                                                                       href="https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate">Wordpress
                        Plugin Boilerplate v 2.6.1</a>. The local repository is continuously updated to the latest
                    version. Source code of this code generator is available in <a
                        href="https://github.com/hasinhayder/plugin-boilerplate-code-generator" target="_blank">Github</a>.
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <form target="_blank" method="POST" action="" role="form">

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="plugin-name">Plugin Name</label>
                    <input type="text" class="form-control" id="plugin-name" name="plugin-name"
                           placeholder="Enter Plugin Name">
                </div>

                <div class="form-group">
                    <label for="plugin-uri">Plugin URI</label>
                    <input type="text" class="form-control" id="plugin-uri" name="plugin-uri" placeholder="Plugin URI">
                </div>

                <div class="form-group">
                    <label for="plugin-description">Description</label>
                    <input type="text" class="form-control" id="plugin-description" name="plugin-description"
                           placeholder="Plugin Description">
                </div>

                <button type="submit" name="submit" value="submit" class="btn btn-default">Submit</button>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="author-name">Author Name</label>
                    <input type="text" class="form-control" id="author-name" name="author-name"
                           placeholder="Author Name">
                </div>
                <div class="form-group">
                    <label for="author-email">Author Email</label>
                    <input type="text" class="form-control" id="author-email" name="author-email"
                           placeholder="Author Email">
                </div>
                <div class="form-group">
                    <label for="author-uri">Author URI</label>
                    <input type="text" class="form-control" id="author-uri" name="author-uri" placeholder="Author URI">
                </div>
            </div>
        </form>

    </div>
</div>
</body>
</html>