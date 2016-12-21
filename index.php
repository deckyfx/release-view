<?php
    ini_set("error_reporting", E_ALL);
    date_default_timezone_set('Asia/Jakarta');

    require_once __DIR__ . '/vendor/autoload.php';
    
    // setup Propel
    require_once __DIR__ . '/generated-conf/config.php';

    use Models\Config;
    use Models\Build;
    use Models\BuildQuery;

    $TITLE = Config::GetConfig("TITLE");
    $builds = BuildQuery::Create()->find();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo($TITLE); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link   href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link   href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet" >
    <link   href="bower_components/bootstrap-fileinput/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <link   href="bower_components/normalize-css/normalize.css" rel="stylesheet" >
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/moment/min/moment.min.js"></script>
    <script src="bower_components/moment/min/moment-with-locales.min.js"></script> 
    <!-- canvas-to-blob.min.js is only needed if you wish to resize images before upload.
     This must be loaded before fileinput.min.js -->
    <script src="bower_components/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js" type="text/javascript"></script>
    <!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview.
         This must be loaded before fileinput.min.js -->
    <script src="bower_components/bootstrap-fileinput/js/plugins/sortable.min.js" type="text/javascript"></script>
    <!-- purify.min.js is only needed if you wish to purify HTML content in your preview for HTML files.
         This must be loaded before fileinput.min.js -->
    <script src="bower_components/bootstrap-fileinput/js/plugins/purify.min.js" type="text/javascript"></script>
    <!-- the main fileinput plugin file -->
    <script src="bower_components/bootstrap-fileinput/js/fileinput.min.js"></script>    
    <!-- optionally if you need a theme like font awesome theme you can include 
        it as mentioned below -->
    <script src="bower_components/bootstrap-fileinput/themes/fa/theme.js"></script>
    <!-- optionally if you need translation for your language then include 
        locale file as mentioned below -->
    <script src="bower_components/bootstrap-fileinput/js/locales/id.js"></script>   

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body role="document">
    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?php echo($TITLE); ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Builds</a></li>   
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">
      <!-- Main jumbotron for a primary marketing message or call to action -->
        <div class="page-header">
            <h1><?php echo($TITLE); ?></h1>
            <button type="button" class="btn btn-success " aria-label="Left Align" data-toggle="modal" data-target="#add-build-modal">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Build
            </button>
        </div>
        <?php
            if (sizeof($builds) > 0) {
                echo('<ul class="list-group">');
                $i = 0;
                foreach($builds as $build) {
                    $simplever =  preg_replace('/\W/im', '_', $build->getVersion());
                    $moment = new \Moment\Moment();
                    $element = '<li class="list-group-item">' . 
                    '<span class="badge">' . $moment->format('l, d M y H:i:s')  . '</span>'  .
                    '<span class="glyphicon ' .  (($i == 0)? 'glyphicon-star':'glyphicon-star-empty')  . '" aria-hidden="true"></span>' .
                    '<span style="margin-left: 5px; margin-right: 5px;">' . $build->getName() . ' (' .  $build->getVersion()  . ') </span>'  .
                    '<a style="margin-left: 5px; margin-right: 5px;" class="btn btn-success" href="' . $build->getUrl() . '" target="_blank">Download</a>'  . 
                    '<a style="margin-left: 5px; margin-right: 5px;" class="btn btn-primary" data-toggle="collapse" data-target="#build_' .  $simplever  . '" aria-expanded="false" aria-controls="build_' .  $simplever  . '">Changes</a>'  .  
                    '<div class="collapse" id="build_' .  $simplever  . '">'  . 
                        '<div class="well"> <span>Changeset: </span>'  . $build->getNote() .'</div> ' .
                    '</div> </li>';
                    echo($element);
                    $i++;
                }
                echo('</ul>');
            } else {
                echo("No Build");
            }
        ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="add-build-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Build</h4>
                </div>
                <div class="modal-body">
                    <form id="build-form">
                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon" id="build-name">Name</span>
                            <input name="build-name" type="text" class="form-control" placeholder="Build Name" aria-describedby="basic-addon1">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon" id="build-version">Version</span>
                            <input name="build-version" type="text" class="form-control" placeholder="Build Version" aria-describedby="basic-addon1">
                        </div>

                        <div class="" style="margin-top: 10px; margin-bottom: 10px;">
                            <label class="control-label">Select File</label>
                            <input id="build-file" name="build-file" type="file" multiple class="file-loading">
                            <script>
                                $("#build-file").fileinput({
                                    uploadUrl: "upload.php", // server upload action
                                    uploadAsync: true,
                                    maxFileCount: 1
                                });
                                $("#build-file").on('change', function(event) {
                                    $('#build-file').fileinput('upload')
                                    $('#build-file').fileinput('disable');
                                });
                                $('#build-file').on('fileuploaded', function(event, data, previewId, index) {
                                    $('#build-file').fileinput('enable');
                                    var form = data.form, 
                                        files = data.files, 
                                        extra = data.extra,
                                        response = data.response, 
                                        reader = data.reader;
                                    console.log('File uploaded triggered');                                 
                                    $('#build-url').val(response.url);
                                });
                            </script>
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon" >URL</span>
                            <input name="build-url" type="text" class="form-control" placeholder="External URL" aria-describedby="basic-addon1" id="build-url">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <label for="exampleTextarea">Build Note</label>
                            <textarea class="form-control" id="build-note" rows="3" name="build-note"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-primary" id="submit-build">
                        <span class=" glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save changes
                    </button>
                </div>
            </div>
         </div>
    </div>

    <script>
        $("#input-id").fileinput({'showUpload':true, 'previewFileType':'any'});
        $('#submit-build').click(function(){
            $.ajax({
                type: "POST",
                url: 'add.php',
                data: $('#build-form').serialize(),
                success: function(data, textStatus, jqXHR){
                    //$('#add-build-modal').modal('toggle');
                    //location.reload();
                }
            });
        });        
    </script>
</body>
</html>