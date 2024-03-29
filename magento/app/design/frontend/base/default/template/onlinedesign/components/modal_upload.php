<?php //if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>

<div class="modal fade" id="dg-myclipart">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>				
                <ul role="tablist" id="upload-tabs" class="nbdesigner_modal_tab">
                    <li class="active" ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'"><a href="#upload-computer" role="tab" data-toggle="tab"><i class="fa fa-cloud-upload visible-xs" aria-hidden="true"></i><span class="hidden-xs"><?php echo $this->__("Upload") ?></span></a></li>					
                    <li ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'"><a href="#uploaded-photo" role="tab" data-toggle="tab"><i class="fa fa-cloud visible-xs" aria-hidden="true"></i><span class="hidden-xs"><?php echo $this->__("Uploaded") ?></span></a></li>
                    <li ng-show="settings['nbdesigner_enable_image_url'] == 'yes'"><a href="#nbdesigner_url" role="tab" data-toggle="tab"><i class="fa fa-link visible-xs" aria-hidden="true"></i><span class="hidden-xs"><?php echo $this->__("Image Url") ?></span></a></li>
                    <li ng-show="settings['nbdesigner_enable_facebook_photo'] == 'yes'"><a href="#nbdesigner_facebook" role="tab" data-toggle="tab"><i class="fa fa-facebook-square visible-xs" aria-hidden="true"></i><span class="hidden-xs"><?php echo $this->__("Facebook") ?></span></a></li>
                    <li ng-if="hasGetUserMedia && !modeMobile" ng-click="initWebcam()" ng-show="settings['nbdesigner_enable_image_webcam'] == 'yes'"><a href="#nbdesigner_webcam" role="tab" data-toggle="tab"><i class="fa fa-camera visible-xs" aria-hidden="true"></i><span class="hidden-xs"><?php echo $this->__("Webcam") ?></span></a></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="upload-computer" ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'">
                        <?php 
                            $login_required = (nbdesigner_get_option('nbdesigner_upload_designs_php_logged_in') && !is_user_logged_in()) ? 1 : 0;
							if($login_required):
                        ?>
                        <p><?php echo $this->__("You need to be logged in to upload images!") ?></p>
                        <?php else: ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label><?php echo $this->__("Choose a file upload") ?></label>
                                    <input type="file" id="files-upload" autocomplete="off" ng-file-select="onFileSelect($files)" accept="image/*"/><br />
                                    <p>
                                        <small><?php echo $this->__("Accept file types") ?>: <strong>png, jpg, gif</strong>
                                        <br /><?php echo $this->__("Max file size") ?>: <strong><span id="nbdesigner_maxsize">{{settings.max_size_upload}}</span> MB</strong><br /> <?php echo $this->__("Min file size") ?>: <strong>{{settings.min_size_upload}} MB</strong></small>
                                    </p>
                                    <?php 
                                        $show_term = nbdesigner_get_option('nbdesigner_upload_show_term');
                                        if($show_term == 'yes'):
                                    ?>                                
                                    <p class="nbdesigner-term"><input id="accept_term" type="checkbox"/><a data-toggle="modal" data-target="#term-modal"><?php echo $this->__("I accept the terms") ?></a></p>
                                    <?php endif; ?>
                                </div>
                            </div>							
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary shadow nbdesigner_upload" id="action-upload" ng-click="startUpload()"><?php echo $this->__("Upload") ?></button>
                                </div>
                            </div>                         
                        </div>
                        <?php endif; ?>
                    </div>										
                    <div class="tab-pane" id="uploaded-photo" ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'">
                        <div class="row" id="dag-files-images">
                            <span class="view-thumb" ng-repeat="url in uploadURL | reverse | limitTo : imgPageSize">
                                <img class="img-responsive img-thumbnail nbdesigner_upload_image shadow hover-shadow" ng-src="{{url}}" ng-click="addImage(url, readyReplaceImage)"  spinner-on-load/>
                            </span>                                                    
                        </div>						
                        <div id="image-load-more" ng-show="(uploadURL.length > 10) && (uploadURL.length > imgPageSize)"><button type="button" style="margin-top: 10px;" class="btn btn-primary shadow nbdesigner_upload" ng-click="imgPageSize = imgPageSize +10"><?php echo $this->__("More") ?></button></div>
                        <div class="progress progress-bar-container" ng-show="loading">
                            <div class="progress-bar progress-bar-striped"  role="progressbar" aria-valuenow="{{progressUpload}}"
                                 aria-valuemin="0" aria-valuemax="100" ng-style="{'width': progressUpload + '%'}" >{{progressUpload}}%</div>
                        </div>                                                
                        <div class="row col-md-12">
                            <span class="help-block"><?php echo $this->__("Click image to add design") ?>.</span>
                        </div>
                    </div>
                    <div class="tab-pane" id="nbdesigner_facebook" ng-show="settings['nbdesigner_enable_facebook_photo'] == 'yes'">
                        <?php 
                            $fbID = nbdesigner_get_option('nbdesigner_facebook_app_id');
                            if($fbID != ''):
                                include_once 'tab_facebook_photo.php'; 
                            else:                            
                        ?>
                        <p><?php echo $this->__("Please fill Facebook app ID") ?></p>
                        <?php endif; ?>
                        <div id="uploaded-facebook"></div>
                        <div>
                            <input type="hidden" id="nbdesigner_fb_next" value=""/>
                            <button style="margin-right: 15px; margin-top: 10px;" id="facebook-load-more" type="button" class="hidden btn btn-primary shadow nbdesigner_upload" ng-click="loadMoreFacebookPhoto()"><?php echo $this->__("More") ?></button>
                            <img id="loading_fb_upload" class="hidden" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/images/ajax-loader.gif'; ?>" />
                        </div>
                    </div>
                    <div class="tab-pane" id="nbdesigner_url" ng-show="settings['nbdesigner_enable_image_url'] == 'yes'">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label><?php echo $this->__("Image URL") ?></label>
                                    <input class="form-control hover-shadow nbdesigner_image_url" ng-model="imageFromUrl"  placeholder="<?php echo $this->__("Enter your image url") ?>"/><br />
                                </div>
                            </div>    
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary shadow nbdesigner_upload"  ng-click="addImageFromUrl()"><?php echo $this->__("Insert") ?></button>
                                </div>
                            </div>                            
                        </div>                        
                    </div>
                    <div ng-if="hasGetUserMedia && !modeMobile" class="tab-pane" id="nbdesigner_webcam" ng-show="settings['nbdesigner_enable_image_webcam'] == 'yes'">
                        <div class="row">
                            <div class="col-xs-12 con-webcam" id="my_camera" ng-show="statusWebcam"></div>    
                            <div class="col-xs-12 con-webcam off" ng-show="!statusWebcam">
                                <i class="fa fa-camera icon-camera" aria-hidden="true"></i>
                            </div>                               
                        </div>
                        <div style="margin-top: 15px;">
                            <button ng-disabled="!statusWebcam" class="btn btn-primary shadow nbdesigner_upload" ng-click="pauseWebcam()"><?php echo $this->__("Pause") ?></button>                     
                            <button ng-disabled="!statusWebcam" class="btn btn-primary shadow nbdesigner_upload" ng-click="unPauseWebcam()"><?php echo $this->__("Un Pause") ?></button>                     
                            <button class="btn btn-primary shadow nbdesigner_upload" ng-click="resetWebcam()"><?php echo $this->__("Stop Webcam") ?></button> 
                            <button ng-disabled="!statusWebcam" class="btn btn-primary shadow nbdesigner_upload" ng-click="takeSnapshot()"><?php echo $this->__("Capture") ?></button>   
                        </div>
                    </div>                          
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="term-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content nbdesigner-term-modal">
                <div class="modal-body">
                    <div class="modal-body">                       
                        <?php echo stripslashes(nbdesigner_get_option('nbdesigner_upload_term')); ?>
                    </div>                   
                </div>                
            </div>    
        </div>          
    </div>    
</div>