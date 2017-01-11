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

    <link   href="bower_components/animate.css/animate.css" rel="stylesheet" >
    <script src="bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>

    <script src="bower_components/underscore/underscore-min.js"></script>   

    <script src="bower_components/rsvp.js/rsvp.min.js"></script>   

    <script src="bower_components/jquery.serializeJSON/jquery.serializejson.min.js"></script>   


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
                <a class="navbar-brand" href="#build"><?php echo($TITLE); ?></a>
            </div>
            <div id="navbar nav nav-tabs" class="navbar-collapse collapse">
                <ul class="nav navbar-nav" role="tablist">
                    <li class="active" aria-controls="builds" role="tab" data-toggle="tab" data-hashroute="build"><a href="#">Builds</a></li>   
                    <li class="" aria-controls="mailinglist" role="tab" data-toggle="tab" data-hashroute="email"><a href="#">Mailing List</a></li>   
                    <li class="" aria-controls="preference" role="tab" data-toggle="tab" data-hashroute="config"><a href="#">Preferences</a></li>   
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container theme-showcase" role="main">
      <!-- Main jumbotron for a primary marketing message or call to action -->
        <div class="page-header">
            <h1><?php echo($TITLE); ?></h1>
        </div>
        <!-- Tab panes -->
        <div class="tab-content" id="tab-content">
            <div role="tabpanel" class="tab-pane active" id="builds-tab">
                <button type="button" class="btn btn-success " aria-label="Left Align" style="margin-bottom: 10px;" data-run="build.clickadd" >
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Build
                </button>                
                <div class="panel panel-info">
                    <div class="panel-heading">Build List</div>
                    <div class="panel-body" style="padding: 0px;">
                        <ul class="list-group" id="build-list" style="margin-bottom: 0px;"></ul>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="mailinglist-tab">
                <button type="button" class="btn btn-success " aria-label="Left Align" style="margin-bottom: 10px;" data-run="email.clickadd" >
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Email
                </button>                
                <div class="panel panel-info">
                    <div class="panel-heading">Email List</div>
                    <div class="panel-body" style="padding: 0px;">
                        <ul class="list-group" id="email-list" style="margin-bottom: 0px;"></ul>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="preference-tab">
                <button type="button" class="btn btn-success " aria-label="Left Align" style="margin-bottom: 10px;" data-run="config.clickadd">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Config
                </button>                
                <div class="panel panel-info">
                    <div class="panel-heading">Config List</div>
                    <div class="panel-body" style="padding: 0px;">
                        <ul class="list-group" id="config-list" style="margin-bottom: 0px;"></ul>
                    </div>
                </div>
            </div>
        </div>        
    </div>

    <!-- Auth Modal -->
    <div class="modal fade" id="auth-modal" tabindex="-1" role="dialog" aria-labelledby="auth-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="auth-modal">Enter Auth Key</h4>
                </div>
                <div class="modal-body">
                    <form id="auth-modal-form">
                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon" id="build-name">Auth Key</span>
                            <input name="authkey" type="text" class="form-control" placeholder="Key" aria-describedby="auth-key" id="authkey">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-primary" data-run="authmodal">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Authorize
                    </button>
                </div>
            </div>
         </div>
    </div>

    <!-- Add Build Modal -->
    <div class="modal fade" id="add-build-modal" tabindex="-1" role="dialog" aria-labelledby="add-build-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Build</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon">Name</span>
                            <input name="build-name" type="text" class="form-control" placeholder="Build Name" aria-describedby="basic-addon1">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon">Version</span>
                            <input name="build-version" type="text" class="form-control" placeholder="Build Version" aria-describedby="basic-addon1">
                        </div>

                        <div class="" style="margin-top: 10px; margin-bottom: 10px;">
                            <label class="control-label">Select File</label>
                            <input name="build-file" type="file" multiple class="file-loading">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon" >URL</span>
                            <input name="build-url" type="text" class="form-control" placeholder="External URL" aria-describedby="basic-addon1">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px; width:100%;">
                            <label for="exampleTextarea">Build Note</label>
                            <textarea rows="3" name="build-note" class="form-control" style="width:100%;"></textarea>
                        </div>
                        <input name="build-id" type="hidden" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-primary" data-run="build.submit">
                        <span class=" glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save changes
                    </button>
                </div>
            </div>
         </div>
    </div>

     <!-- Confirm Remove Buil Modal -->
    <div class="modal fade" id="remove-build-modal" tabindex="-1" role="dialog" aria-labelledby="remove-build-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Remove Build</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <input name="remove-id" type="hidden" class="form-control" placeholder="Key" aria-describedby="confirm-remove-key" >
                    </form>
                    You cannot undo this action<br/>Remove Build <span></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-danger" data-run="build.remove">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove
                    </button>
                </div>
            </div>
         </div>
    </div>

    <!-- Add Email Modal -->
    <div class="modal fade" id="add-email-modal" tabindex="-1" role="dialog" aria-labelledby="add-email-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Email</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon">Email</span>
                            <input name="email-name" id="email-name" type="text" class="form-control" placeholder="Email" aria-describedby="basic-addon1">
                        </div>
                        <input name="email-id" type="hidden" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-primary" data-run="email.submit">
                        <span class=" glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save changes
                    </button>
                </div>
            </div>
         </div>
    </div>

     <!-- Confirm Remove Email Modal -->
    <div class="modal fade" id="remove-email-modal" tabindex="-1" role="dialog" aria-labelledby="remove-email-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Remove Email</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <input name="remove-id" type="hidden" class="form-control" placeholder="Key" aria-describedby="confirm-remove-key" >
                    </form>
                    You cannot undo this action<br/>Remove Email <span></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-danger" data-run="email.remove">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove
                    </button>
                </div>
            </div>
         </div>
    </div>

    <!-- Add Config Modal -->
    <div class="modal fade" id="add-config-modal" tabindex="-1" role="dialog" aria-labelledby="add-config-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Build</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon">Name</span>
                            <input name="config-name" type="text" class="form-control" placeholder="Config Name" aria-describedby="basic-addon1">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon">Value</span>
                            <input name="config-value" type="text" class="form-control" placeholder="Config Value" aria-describedby="basic-addon1">
                        </div>

                        <div class="input-group" style="margin-top: 10px; margin-bottom: 10px; width:100%;">
                            <label for="">Value Type</label>
                            <div class="dropdown">
                                <button name="config-type-dropdown" class="btn btn-default dropdown-toggle" type="button" id="config-type-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span>Type: </span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="config-type-dropdown">
                                    <li><a href="javascript:void(0)" data-value="string">String</a></li>
                                    <li><a href="javascript:void(0)" data-value="int">Integer</a></li>
                                    <li><a href="javascript:void(0)" data-value="double">Double</a></li>
                                    <li><a href="javascript:void(0)" data-value="boolean">Boolean</a></li>
                                </ul>
                                <input name="config-type" type="hidden" value="">
                            </div>
                        </div>  

                        <div class="input-group">
                            <label for="">Enabled</label>
                            <br />
                            <input type="checkbox" aria-label="..." name="config-enabled" value="true">
                        </div>                      

                        <input name="config-id" type="hidden" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-primary" data-run="config.submit">
                        <span class=" glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save changes
                    </button>
                </div>
            </div>
         </div>
    </div>

     <!-- Confirm Remove Config Modal -->
    <div class="modal fade" id="remove-config-modal" tabindex="-1" role="dialog" aria-labelledby="remove-config-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" >Remove Build</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <input name="remove-id" type="hidden" class="form-control" placeholder="Key" aria-describedby="confirm-remove-key" >
                    </form>
                    You cannot undo this action<br/>Remove Config <span></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">                        
                        <span class=" glyphicon glyphicon-remove" aria-hidden="true"></span> Close
                    </button>
                    <button type="button" class="btn btn-danger" data-run="config.remove">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Remove
                    </button>
                </div>
            </div>
         </div>
    </div>

    <script src="assets/js/main.js"></script>    
</body>
</html>