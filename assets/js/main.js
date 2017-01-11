$( document ).ready(function() {
	function App () {
		this.remote_path = 'action.php',
		this.method = 'POST'
		this.action = {
			build: {
				list: function(){
					var callback 	= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: { action: 'build_list' }
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		var list 			= $('#build-list');
		            		response_data 		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		list.empty();
		            		this.build_list = [];
		            		for (var i = 0; i < response_data.builds.length; i++) {
		            			var item = response_data.builds[i];
		            			var item_element = '<li class="list-group-item" id="build_{BUILD_ID}"><span class="badge">{BUILD_DATE}</span><span class="{BUILD_ICON}" aria-hidden="true"></span><span style="margin-left: 5px; margin-right: 5px;">{BUILD_NAME} ({BUILD_VERSION})</span><span class="btn">&nbsp;</span><div class="dropdown pull-right" style="margin-right:10px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span><span class="caret"></span></button> <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"><li><a href="javascript:void(0)" data-run="build.clickedit" data-idx="{BUILD_IDX}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li> <li><a href="javascript:void(0)"  data-run="build.clicknotify" data-idx="{BUILD_IDX}"><i class="glyphicon glyphicon-envelope"></i> Notify By Mail</a></li> <li><a href="javascript:void(0)" data-run="build.clickremove" data-idx="{BUILD_IDX}"><i class="glyphicon glyphicon-remove"></i>  Remove</a></li></ul></div><a style="margin-left: 5px; margin-right: 5px;" class="btn btn-success pull-right" href="{BUILD_URL}" target="_blank">Download</a><a style="margin-left: 5px; margin-right: 5px;" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#build_{BUILD_ID}_changeset" aria-expanded="false" aria-controls="build_{BUILD_ID}_changeset">Changes</a><div class="collapse" id="build_{BUILD_ID}_changeset"><pre style="margin-top:10px;">Changeset:\n{BUILD_NOTE}</pre></div></li>';
		            			var replace = [
		            				'{BUILD_ID}',
		            				'{BUILD_IDX}',
		            				'{BUILD_ICON}',
		            				'{BUILD_NAME}',
		            				'{BUILD_VERSION}',
		            				'{BUILD_DATE}',
		            				'{BUILD_URL}',
		            				'{BUILD_NOTE}'
		            			];
		            			var replacement = [
		            				item.Id,
		            				i,
		            				(i == 0)? 'glyphicon glyphicon-star':'glyphicon glyphicon-star-empty',
		            				item.Name,
		            				item.Version,
		            				new moment(item.CreatedAt).format('Do MMMM YYYY HH:mm:ss'),
		            				item.Url,
		            				item.Note
		            			];
		            			for (n = 0; n < replace.length; n++) {
		            				item_element = item_element.replace(new RegExp(replace[n], 'g'), replacement[n]);
		            			}
		            			list.append( $(item_element) );
		            			this.build_list.push(item);
		            		}
							list.find('[data-run]').each($.proxy(function(i, e) {
								$(e).click($.proxy( function( el, evt ){
									this.run($(el).data('run'), evt);				
								}, this, $(e) ));
							}, this) );								
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  	}.bind(this));
				  	return this;
				},
				clickadd: function(){
					this.run('auth', function(){
			    		this.run('build.openform');
			    	}.bind(this));
			    	return this;
				},				
				clickedit: function(ev){
					this.run('auth', function(){
			    		this.run('build.openform', ev);
			    	}.bind(this));
			    	return this;
				},
				clicknotify: function(ev){
			    	return this;
				},
				clickremove: function(ev){
					this.run('auth', function(){
			    		this.run('build.confirmremove', ev);
			    	}.bind(this));
			    	return this;
				},
				openform: function() {
					var dialog_title 	= "Add Build";
					var modal 			= $('#add-build-modal');
					var form 			= modal.find('form');
					if (!form.data('initialized')) {
				        // ---------------
					    // other GUI First Time init
					    // ---------------
					    var file_upload	= form.find('[name=build-file]');
					    file_upload.fileinput({
					        uploadUrl: "action.php", // server upload action
					        uploadExtraData: { action: 'build_upload' },
					        uploadAsync: true,
					        maxFileCount: 1
					    });
					    file_upload.on('change', function(event) {
					        file_upload.fileinput('upload')
					        file_upload.fileinput('disable');
					    });
					    file_upload.on('fileuploaded', function(event, data, previewId, index) {
					        file_upload.fileinput('enable');
					        var upload = data.response.data.upload;
					        var form = upload.form, 
					            files = upload.files, 
					            extra = upload.extra,
					            response = upload.response, 
					            reader = upload.reader;                               
					        form.find('[name=build-url]').val(upload.url);
					    }.bind(this));
					    form.data('initialized', true);
					} 

					if (arguments[0] !== undefined) {
						var ev = arguments[0];					
						var el = $(ev.target);
						var idx = el.data('idx');
						var item = this.build_list[idx];
						dialog_title = "Edit Build ID: " + item.Id;						
						form.find('[name=build-name]').val(item.Name);
						form.find('[name=build-version]').val(item.Version);
						form.find('[name=build-url]').val(item.Url);
						form.find('[name=build-note]').val(item.Note);
						form.find('[name=build-id]').val(item.Id);
					} else {
		    			form[0].reset();
					}
					modal.find('h4:first').html(dialog_title);
				  	modal.modal('toggle');
		    		return this;
				},
				submit: function(){
					var modal 			= $('#add-build-modal');
					var form 			= modal.find('form');	
			    	var post_data 		= form.serializeJSON();
			    	post_data.action 	= 'build_submit';
			    	var callback 		= (arguments[0] === undefined)? function(){}:arguments[0];
			        return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: post_data
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		response_data		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		this.notify('Build ' + (post_data['build-id']? 'updated':'added') + '!', 1);
		            		this.run('build.list');	
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  		modal.modal('toggle');
				  	}.bind(this));
				  	return this;
				},
				confirmremove: function() {
					var ev 				= arguments[0];					
					var el 				= $(ev.target);
					var idx 			= el.data('idx');
					var item 			= this.build_list[idx];
					var modal 			= $('#remove-build-modal');
					modal.find('[name=remove-id]').val(item.Id);
					modal.find('.modal-body span').html('ID: ' + item.Id + ', Name: ' + item.Name + ', Version: ' + item.Version);
					modal.modal('toggle');
		    		return this;
				},
				remove: function(){
					var modal 			= $('#remove-build-modal');
					var form 			= modal.find('form');
					var post_data 		= form.serializeJSON();
			    	post_data.action 	= 'build_remove';
			    	var callback 		= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: post_data
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		response_data		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		this.notify('Build removed!', 1);
		            		this.run('build.list');	
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  		$('#remove-build-modal').modal('toggle');
				  	}.bind(this));
				  	return this;
				}
		    },
		    email: {
				list: function(){
					var callback 	= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: { action: 'email_list' }
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		var list 			= $('#email-list');
		            		response_data = response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		list.empty();
		            		this.email_list = [];
		            		for (var i = 0; i < response_data.emails.length; i++) {
		            			var item = response_data.emails[i];
		            			var item_element = '<li class="list-group-item" id="email_{EMAIL_ID}"> <span style="margin-left: 5px; margin-right: 5px;">{EMAIL_ADDRESS}</span> <span class="btn">&nbsp;</span> <div class="dropdown pull-right" style="margin-right:10px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <span class="caret"></span></button> <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"><li><a href="javascript:void(0)" data-run="email.clickedit" data-idx="{EMAIL_IDX}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li><li><a href="javascript:void(0)" data-run="email.clickremove"  data-idx="{EMAIL_IDX}"><i class="glyphicon glyphicon-remove"></i> Remove</a></li></ul> </div></li>';
		            			var replace = [
		            				'{EMAIL_ID}',
		            				'{EMAIL_IDX}',
		            				'{EMAIL_ADDRESS}'
		            			];
		            			var replacement = [
		            				item.Id,
		            				i,
		            				item.Email
		            			];
		            			for (n = 0; n < replace.length; n++) {
		            				item_element = item_element.replace(new RegExp(replace[n], 'g'), replacement[n]);
		            			}
		            			list.append( $(item_element) );
		            			this.email_list.push(item);
		            		}
							list.find('[data-run]').each($.proxy(function(i, e) {
								$(e).click($.proxy( function( el, evt ){
									this.run($(el).data('run'), evt);				
								}, this, $(e) ));
							}, this) );
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  	}.bind(this));
				  	return this;
				},
				clickadd: function(){
					this.run('auth', function(){
			    		this.run('email.openform');
			    	}.bind(this));
			    	return this;
				},
				clickedit: function(ev){
					this.run('auth', function(){
			    		this.run('email.openform', ev);
			    	}.bind(this));
			    	return this;
				},
				clickremove: function(ev){
					this.run('auth', function(){
			    		this.run('email.confirmremove', ev);
			    	}.bind(this));
			    	return this;
				},
				openform: function() {	
					var dialog_title 	= "Add Email";
					var modal 			= $('#add-email-modal');
					var form 			= modal.find('form');
					if (arguments[0] !== undefined) {
						var ev 			= arguments[0];					
						var el 			= $(ev.target);
						var idx 		= el.data('idx');
						var item 		= this.email_list[idx];
						dialog_title 	= "Edit Email ID: " + item.Id;						
						form.find('[name=email-name]').val(item.Email);
						form.find('[name=email-id]').val(item.Id);
					} else {
		    			form[0].reset();
					}
					modal.find('h4:first').html(dialog_title);
				  	modal.modal('toggle');
		    		return this;
				},
				submit: function(){
					var modal 			= $('#add-email-modal');
					var form 			= modal.find('form');
			    	var post_data 		= form.serializeJSON();
			    	post_data.action 	= 'email_submit';
			    	var callback 		= (arguments[0] === undefined)? function(){}:arguments[0];
			        return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: post_data
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		response_data		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		this.notify('Email ' + (post_data['email-id']? 'updated':'added') + '!', 1);
		            		this.run('email.list');	
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  		modal.modal('toggle');
				  	}.bind(this));
				  	return this;
				},
				confirmremove: function() {
		    		var ev 				= arguments[0];					
					var el 				= $(ev.target);
					var idx 			= el.data('idx');
					var item 			= this.email_list[idx];
					var modal 			= $('#remove-email-modal');
					modal.find('[name=remove-id]').val(item.Id);
					modal.find('.modal-body span').html('ID: ' + item.Id + ', Email: ' + item.Email);
					modal.modal('toggle');
		    		return this;
				},
				remove: function(){
					var modal 			= $('#remove-email-modal');
					var form 			= modal.find('form');
					var post_data 		= form.serializeJSON();
			    	post_data.action 	= 'email_remove';
			    	var callback 		= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: post_data
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		response_data		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		this.notify('Email removed!', 1);
		            		this.run('email.list');
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  		$('#remove-email-modal').modal('toggle');
				  	}.bind(this));
				}
		    },
		    config:  {
				list: function(){
					var callback 	= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: { action: 'config_list' }
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		var list 			= $('#config-list');
		            		response_data 		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		list.empty();
		            		this.config_list = [];
		            		for (var i = 0; i < response_data.configs.length; i++) {
		            			var item = response_data.configs[i];
		            			var item_element = '<li class="list-group-item" id="build_{CONFIG_ID}"><span class="badge pull-left" style="margin-top: 7px;">{CONFIG_TYPE}</span><span style="margin-left: 5px;margin-right: 5px;font-weight: bolder;">{CONFIG_NAME} :</span><span style="margin-left: 5px; margin-right: 5px;">{CONFIG_VALUE}</span><span class="btn">&nbsp;</span><div class="dropdown pull-right" style="margin-right:10px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span><span class="caret"></span></button> <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"><li><a href="javascript:void(0)" data-run="config.clickedit" data-idx="{CONFIG_IDX}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>  <li><a href="javascript:void(0)" data-run="config.clickremove" data-idx="{CONFIG_IDX}"><i class="glyphicon glyphicon-remove"></i>  Remove</a></li></ul></div></li>';
		            			var replace = [
		            				'{CONFIG_ID}',
		            				'{CONFIG_IDX}',
		            				'{CONFIG_TYPE}',
		            				'{CONFIG_NAME}',
		            				'{CONFIG_VALUE}'
		            			];
		            			switch (item.Type) {
		            				case 'string': {
		            					item.TypeText = 'String';
		            				} break;
		            				case 'int': {
		            					item.TypeText = 'Integer';
		            				} break;
		            				case 'double': {
		            					item.TypeText = 'Double';
		            				} break;
		            				case 'boolean': {
		            					item.TypeText = 'Boolean';
		            				} break;
		            				default: {
		            					item.TypeText = 'Other';
		            				} break;
		            			}
		            			var replacement = [
		            				item.Id,
		            				i,
		            				item.TypeText,
		            				item.Name,
		            				item.Value
		            			];
		            			for (n = 0; n < replace.length; n++) {
		            				item_element = item_element.replace(new RegExp(replace[n], 'g'), replacement[n]);
		            			}
		            			list.append( $(item_element) );
		            			this.config_list.push(item);
		            		}
							list.find('[data-run]').each($.proxy(function(i, e) {
								$(e).click($.proxy( function( el, evt ){
									this.run($(el).data('run'), evt);				
								}, this, $(e) ));
							}, this) );
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  	}.bind(this));
				  	return this;
				},
				clickadd: function(){
					this.run('auth', function(){
			    		this.run('config.openform');
			    	}.bind(this));
				  	return this;
				},
				clickedit: function(ev){
					this.run('auth', function(){
			    		this.run('config.openform', ev);
			    	}.bind(this));
				  	return this;
				},
				clickremove: function(ev){
					this.run('auth', function(){
			    		this.run('config.confirmremove', ev);
			    	}.bind(this));
				  	return this;
				},
				openform: function() {	
					var dialog_title 	= "Add Config";
					var modal 			= $('#add-config-modal');
					var form 			= modal.find('form');		
					if (!form.data('initialized')) {
				        // ---------------
					    // other GUI First Time init
					    // ---------------
					    var dropdown 	= $('#config-type-dropdown');
					    var dropdownmenu= dropdown.next().find('li');
					    dropdownmenu.on('click', function(){
					    	var a 		= $(this).find('a');
					    	dropdown.val(a.html());
					    	dropdown.find('span:first-child').html('Type: ' + a.html());
					    	form.find('[name=config-type]').val(a.data('value'));
						});
					    form.data('initialized', true);
					}
					if (arguments[0] !== undefined) {
						var ev 			= arguments[0];					
						var el 			= $(ev.target);
						var idx 		= el.data('idx');
						var item 		= this.config_list[idx];
						dialog_title 	= "Edit Config ID: " + item.Id;						
						form.find('[name=config-name]').val(item.Name);
						form.find('[name=config-value]').val(item.Value);
						form.find('[name=config-type]').val(item.Type);
						var dropdown 	= $('#config-type-dropdown');
						var a 			= dropdown.next().find('a[data-value='+item.Type+']');
						dropdown.val(a.html());
					    dropdown.find('span:first-child').html('Type: ' + a.html());
						form.find('[name=config-enabled]').prop('checked', item.Enabled);
						form.find('[name=config-id]').val(item.Id);	
					} else {
		    			form[0].reset();
					}
					modal.find('h4:first').html(dialog_title);
				  	modal.modal('toggle');
		    		return this;
				},
				submit: function(){
					var modal 			= $('#add-config-modal');
					var form 			= modal.find('form');	
			    	var post_data 		= form.serializeJSON();
			    	post_data.action 	= 'config_submit';
			    	var callback 		= (arguments[0] === undefined)? function(){}:arguments[0];
			        return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: post_data
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		response_data		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		this.notify('Config ' + (post_data['config-id']? 'updated':'added') + '!', 1);
		            		this.run('config.list');	
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  		modal.modal('toggle');
				  	}.bind(this));
				  	return this;
				},
				confirmremove: function() {
					var ev 				= arguments[0];					
					var el 				= $(ev.target);
					var idx 			= el.data('idx');
					var item 			= this.build_list[idx];
					var modal 			= $('#remove-config-modal');
					modal.find('[name=remove-id]').val(item.Id);
					modal.find('.modal-body span').html('ID: ' + item.Id + ', Name: ' + item.Name + ', Version: ' + item.Version);
					modal.modal('toggle');
		    		return this;
				},
				remove: function(){
					var modal 			= $('#remove-config-modal');
					var form 			= modal.find('form');
					var post_data 		= form.serializeJSON();
			    	post_data.action 	= 'config_remove';
			    	var callback 		= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: post_data
			        }).done(function(response) {
		            	try {
		            		var response_data 	= JSON.parse(response);
		            		var success 		= response_data.success;
		            		response_data		= response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		this.notify('Config removed!', 1);
		            		this.run('config.list');	
							callback.apply(this, []);
		            	} catch (e) {
							this.notify(e, 0);
		            	}
				  	}.bind(this))
				  	.fail(function() {
						this.notify('Request failed', 0);
				  	}.bind(this))
				  	.always(function() {
				  		$('#remove-build-modal').modal('toggle');
				  	}.bind(this));
				  	return this;
				}
			},
			auth: function(){
				arguments 		= Array.prototype.slice.call(arguments);
				var callback 	= (arguments[0] === undefined)? function(){}:arguments[0];
				var authkey 	= $('#authkey').val();
				return $.ajax({
	                type 	: this.method,
	                url 	: this.remote_path,
	                data 	: { action: 'auth', authkey: authkey },
	            }).done(function(response) {
	            	try {
	            		var response_data 	= JSON.parse(response);
	            		var success 		= response_data.success;
	            		response_data 		= response_data.data;
	            		if (!success) {
	            			throw new Error(response_data.message);
	            		}
						callback.apply(this, []);
	            	} catch (e) {
						this.notify(e, 0);
						this.run('authmodal');
	            	}
			  	}.bind(this))
			  	.fail(function() {
					this.notify('Request failed', 0);
					this.run('authmodal');
			  	}.bind(this))
			  	.always(function() {
			  	}.bind(this));
    			return this;
    		},
    		authmodal: function(){
    			$('#auth-modal').modal('toggle');
    			return this;
    		},
		};
		this.routes = {
			build: function(){
				$('#tab-content > div').hide();
	    		$('#builds-tab').show();
   				this.run('build.list');
    			return this;
			},
			email: function(){
				$('#tab-content > div').hide();
	    		$('#mailinglist-tab').show();
   				this.run('email.list');
    			return this;
			},
			config: function(){
				$('#tab-content > div').hide();
	    		$('#preference-tab').show();
	    		this.run('config.list');
    			return this;
			},
		};
		this.run = function(){
	    	arguments = Array.prototype.slice.call(arguments);
	    	if (arguments.length == 0) {
	    		return;
	    	}
	    	var path = arguments[0];
	    	arguments.shift();
	    	var script = this.action;
	    	try {
		    	for (var i=0, path=path.split('.'), len=path.length; i<len; i++){
			        script = script[path[i]];
			    };
	    	} catch (e) { 
	    		// action not found
	    	}
	    	if (script !== undefined && typeof script === "function") {
	    		return script.apply(this, arguments);
	    	}
	    };
	    this.route = function(){    	
	    	arguments = Array.prototype.slice.call(arguments);
	    	if (arguments.length == 0) {
	    		return;
	    	}
	    	var path = arguments[0].replace(/^\#/, "");
	    	arguments.shift();
	    	var script = this.routes;
	    	try {
		    	for (var i=0, path=path.split('.'), len=path.length; i<len; i++){
			        script = script[path[i]];
			    };
	    	} catch (e) { 
	    		// routes not found
	    	}
	    	if (script !== undefined && typeof script === "function") {
	    		return script.apply(this, arguments);
	    	}
	    };
	    this.notify = function(message, status) {
	    	switch (status) {
	    		case 0: {
	    			icon = 'glyphicon glyphicon-warning-sign',
					type = 'danger';
	    		} break;
	    		case 1: {
	    			icon = 'glyphicon glyphicon-ok';
	    			type = 'success';
	    		} break
	    	}
	    	$.notify({
				// options
				icon 	: icon,
				message : message,
			}, this.createNotifyConfig({ type: type }) );
	    };	    
	    this.createNotifyConfig = function(override){
	    	var _notify_config = {
				// settings
				element: 'body',
				position: null,
				type: "info",
				allow_dismiss: true,
				newest_on_top: false,
				showProgressbar: false,
				placement: {
					from: "top",
					align: "center"
				},
				offset: 20,
				spacing: 10,
				z_index: 99999,
				delay: 2000,
				timer: 500,
				url_target: '_blank',
				mouse_over: null,
				animate: {
					enter: 'animated fadeInDown',
					exit: 'animated fadeOutUp'
				},
				onShow: null,
				onShown: null,
				onClose: null,
				onClosed: null,
				icon_type: 'class',
				template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
					'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
					'<span data-notify="icon"></span> ' +
					'<span data-notify="title">{1}</span> ' +
					'<span data-notify="message">{2}</span>' +
					'<div class="progress" data-notify="progressbar">' +
						'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
					'</div>' +
					'<a href="{3}" target="{4}" data-notify="url"></a>' +
				'</div>' 
			};
			return _.extend(_notify_config, override);
	    };
	    this.getLocationHash = function(){
	    	return window.location.hash;
	    };
	    this.setLocationHash = function(hash){
	    	window.location.hash = "#" + hash;
	    };
	    this.init = function() {
		    $('[data-hashroute]').each($.proxy(function(i, e) {
				$(e).click($.proxy( function( el, evt ){
					this.setLocationHash($(el).data('hashroute'));					
				}, this, $(e) ));
			}, this) );

			$('[data-run]').each($.proxy(function(i, e) {
				$(e).click($.proxy( function( el, evt ){
					this.run($(el).data('run'));				
				}, this, $(e) ));
			}, this) );

		    $(window).on('hashchange', function() {
				this.route(this.getLocationHash());
			}.bind(this));
			this.route(this.getLocationHash());
	    }
	    this.init();
	}

    window._app = new App(); 
    _app.run('build.list');
})