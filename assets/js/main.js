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
		            		var response_data = JSON.parse(response);
		            		var success = response_data.success;
		            		response_data = response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		$('#build-list').empty();
		            		this.build_list = [];
		            		for (var i = 0; i < response_data.builds.length; i++) {
		            			var item = response_data.builds[i];
		            			var item_element = '<li class="list-group-item" id="build_{BUILD_ID}"><span class="badge">{BUILD_DATE}</span><span class="{BUILD_ICON}" aria-hidden="true"></span><span style="margin-left: 5px; margin-right: 5px;">{BUILD_NAME} ({BUILD_VERSION})</span><span class="btn">&nbsp;</span><div class="dropdown pull-right" style="margin-right:10px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span><span class="caret"></span></button> <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"><li><a href="#" class="build-edit-button" data-idx="{BUILD_IDX}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li> <li><a href="#" class="build-notify-button" data-idx="{BUILD_IDX}"><i class="glyphicon glyphicon-envelope"></i> Notify By Mail</a></li> <li><a href="#" class="build-remove-button" data-idx="{BUILD_IDX}"><i class="glyphicon glyphicon-remove"></i>  Remove</a></li></ul></div><a style="margin-left: 5px; margin-right: 5px;" class="btn btn-success pull-right" href="{BUILD_URL}" target="_blank">Download</a><a style="margin-left: 5px; margin-right: 5px;" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#build_{BUILD_ID}_changeset" aria-expanded="false" aria-controls="build_{BUILD_ID}_changeset">Changes</a><div class="collapse" id="build_{BUILD_ID}_changeset"><pre style="margin-top:10px;">Changeset:\n{BUILD_NOTE}</pre></div></li>';
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
		            			$('#build-list').append( item_element );
		            			this.build_list.push(item);
		            		}		            		
	            			$('.build-edit-button').click(function(ev){
	            				this.run('auth', function(){
						    		this.run('build.openform', ev);
						    	}.bind(this));
	            			}.bind(this));
	            			$('.build-notify-button').click(function(ev){
	            				this.run('auth', function(){
						    		this.run('maillist.notify', ev);
						    	}.bind(this));	            				
	            			}.bind(this));
	            			$('.build-remove-button').click(function(ev){	            				
	            				this.run('auth', function(){
						    		this.run('build.confirmremove', ev);
						    	}.bind(this));
	            			}.bind(this));
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
				},
				openform: function() {	
					var dialog_title = "Add Build";
				    $('#add-build-modal').find('#add-build-modal-submit').off("click");
					if (arguments[0] !== undefined) {
						var ev = arguments[0];					
						var el = $(ev.target);
						var idx = el.data('idx');
						var item = this.build_list[idx];
						dialog_title = "Edit Build ID: " + item.Id;						
						$('#add-build-modal').find('#build-name').val(item.Name);
						$('#add-build-modal').find('#build-version').val(item.Version);
						$('#add-build-modal').find('#build-url').val(item.Url);
						$('#add-build-modal').find('#build-note').val(item.Note);
						$('#add-build-modal').find('#build-id').val(item.Id);
					} else {
		    			$('#add-build-modal').find('#add-build-modal-form')[0].reset();	
					}
					$('#add-build-modal').find('#add-build-modal-submit').click(function(){
				    	this.run('build.submit', function(){
				    		$('#add-build-modal').modal('toggle'); 	
				    		this.run('build.list');	
				    	}.bind(this));
				    }.bind(this)); 
					$('#add-build-modal').find('.modal-title').html(dialog_title);	
		    		$('#add-build-modal').modal('toggle');
		    		return this;
				},
				submit: function(){
			    	var post_data 		= $('#add-build-modal').find('#add-build-modal-form').serializeJSON();
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
				},
				confirmremove: function() {
					var ev = arguments[0];					
					var el = $(ev.target);
					var idx = el.data('idx');
					var item = this.build_list[idx];
					$('#remove-build-modal').find('#remove-build-modal-submit').off("click");
					$('#remove-build-modal').find('#remove-id').val(item.Id);
					$('#remove-build-modal').find('#remove-build-modal-text').html('ID: ' + item.Id + ', Name: ' + item.Name + ', Version: ' + item.Version);
					$('#remove-build-modal').find('#remove-build-modal-submit').click(function(){
				    	this.run('build.remove', function(){
				    		$('#remove-build-modal').modal('toggle'); 	
				    		this.run('build.list');	
				    	});
				    }.bind(this));	
					$('#remove-build-modal').modal('toggle');
		    		return this;
				},
				remove: function(){
					var post_data 		= $('#remove-build-modal').find('#remove-build-modal-form').serializeJSON();
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
				}
		    },
		    maillist: {
				list: function(){
					var callback 	= (arguments[0] === undefined)? function(){}:arguments[0];
					return $.ajax({
			            type 	: this.method,
			            url 	: this.remote_path,
			            data 	: { action: 'email_list' }
			        }).done(function(response) {
		            	try {
		            		var response_data = JSON.parse(response);
		            		var success = response_data.success;
		            		response_data = response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		$('#email-list').empty();
		            		this.email_list = [];
		            		for (var i = 0; i < response_data.emails.length; i++) {
		            			var item = response_data.emails[i];
		            			var item_element = '<li class="list-group-item" id="email_{EMAIL_ID}"> <span style="margin-left: 5px; margin-right: 5px;">{EMAIL_ADDRESS}</span> <span class="btn">&nbsp;</span> <div class="dropdown pull-right" style="margin-right:10px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <span class="caret"></span></button> <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"><li><a href="#" class="email-edit-button" data-idx="{EMAIL_IDX}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li><li><a href="#" class="email-remove-button" data-idx="{EMAIL_IDX}"><i class="glyphicon glyphicon-remove"></i> Remove</a></li></ul> </div></li>';
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
		            			$('#email-list').append( item_element );
		            			this.email_list.push(item);
		            		}
	            			$('.email-edit-button').click(function(ev){
	            				this.run('auth', function(){
						    		this.run('maillist.openform', ev);
						    	}.bind(this));
	            			}.bind(this));
	            			$('.email-remove-button').click(function(ev){	            				
	            				this.run('auth', function(){
						    		this.run('maillist.confirmremove', ev);
						    	}.bind(this));
	            			}.bind(this));
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
				},
				openform: function() {	
					var dialog_title = "Add Email";
				    $('#add-email-modal').find('#add-email-modal-submit').off("click");
					if (arguments[0] !== undefined) {
						var ev = arguments[0];					
						var el = $(ev.target);
						var idx = el.data('idx');
						var item = this.email_list[idx];
						dialog_title = "Edit Email ID: " + item.Id;						
						$('#add-email-modal').find('#email-name').val(item.Email);
						$('#add-email-modal').find('#email-id').val(item.Id);
					} else {
		    			$('#add-email-modal').find('#add-email-modal-form')[0].reset();	
					}
					$('#add-email-modal').find('#add-email-modal-submit').click(function(){
				    	this.run('maillist.submit', function(){
				    		$('#add-email-modal').modal('toggle'); 	
				    		this.run('maillist.list');	
				    	}.bind(this));
				    }.bind(this)); 
					$('#add-email-modal').find('.modal-title').html(dialog_title);	
		    		$('#add-email-modal').modal('toggle');
		    		return this;
				},
				submit: function(){
			    	var post_data 		= $('#add-email-modal').find('#add-email-modal-form').serializeJSON();
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
				},
				confirmremove: function() {
					var ev = arguments[0];					
					var el = $(ev.target);
					var idx = el.data('idx');
					var item = this.email_list[idx];
					$('#remove-email-modal').find('#remove-email-modal-submit').off("click");
					$('#remove-email-modal').find('#remove-id').val(item.Id);
					$('#remove-email-modal').find('#remove-email-modal-text').html('ID: ' + item.Id + ', Email: ' + item.Email);
					$('#remove-email-modal').find('#remove-email-modal-submit').click(function(){
				    	this.run('maillist.remove', function(){
				    		$('#remove-email-modal').modal('toggle'); 	
				    		this.run('maillist.list');	
				    	}.bind(this));
				    }.bind(this));	
					$('#remove-email-modal').modal('toggle');
		    		return this;
				},
				remove: function(){
					var post_data 		= $('#remove-email-modal').find('#remove-email-modal-form').serializeJSON();
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
		            		var response_data = JSON.parse(response);
		            		var success = response_data.success;
		            		response_data = response_data.data;
		            		if (!success) {
		            			throw new Error(response_data.message);
		            		}
		            		$('#config-list').empty();
		            		this.config_list = [];
		            		for (var i = 0; i < response_data.configs.length; i++) {
		            			var item = response_data.configs[i];
		            			var item_element = '<li class="list-group-item" id="build_{CONFIG_ID}"><span class="badge pull-left" style="margin-top: 7px;">{CONFIG_TYPE}</span><span style="margin-left: 5px;margin-right: 5px;font-weight: bolder;">{CONFIG_NAME} :</span><span style="margin-left: 5px; margin-right: 5px;">{CONFIG_VALUE}</span><span class="btn">&nbsp;</span><div class="dropdown pull-right" style="margin-right:10px;"><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span><span class="caret"></span></button> <ul class="dropdown-menu" aria-labelledby="dropdownMenu1"><li><a href="#" class="config-edit-button" data-idx="{CONFIG_IDX}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>  <li><a href="#" class="config-remove-button" data-idx="{CONFIG_IDX}"><i class="glyphicon glyphicon-remove"></i>  Remove</a></li></ul></div></li>';
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
		            			$('#config-list').append( item_element );
		            			this.config_list.push(item);
		            		}
	            			$('.config-edit-button').click(function(ev){
	            				this.run('auth', function(){
						    		this.run('config.openform', ev);
						    	}.bind(this));
	            			}.bind(this));
	            			$('.config-remove-button').click(function(ev){	            				
	            				this.run('auth', function(){
						    		this.run('config.confirmremove', ev);
						    	}.bind(this));
	            			}.bind(this));
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
				},
				openform: function() {	
					var dialog_title = "Add Config";
				    $('#add-config-modal').find('#add-config-modal-submit').off("click");
					if (arguments[0] !== undefined) {
						var ev = arguments[0];					
						var el = $(ev.target);
						var idx = el.data('idx');
						var item = this.config_list[idx];
						dialog_title = "Edit Config ID: " + item.Id;						
						$('#add-config-modal').find('#config-name').val(item.Name);
						$('#add-config-modal').find('#config-value').val(item.Value);
						//$('#add-config-modal').find('#config-type-dropdown').val($(this).find('a').html());
				    	//$('#add-config-modal').find('#config-type-dropdown span:first-child').html('Type: ' + $(this).find('a').html());
				    	//$('#add-config-modal').find('#config-type').val($(this).find('a').data('value'));
						$('#add-config-modal').find('#config-enabled').prop('checked', item.Enabled?);
						$('#add-config-modal').find('#config-id').val(item.Id);	
					} else {
		    			$('#add-config-modal').find('#add-config-modal-form')[0].reset();	
					}
					$('#add-config-modal').find('#add-config-modal-submit').click(function(){
				    	this.run('config.submit', function(){
				    		$('#add-config-modal').modal('toggle'); 	
				    		this.run('config.list');	
				    	}.bind(this));
				    }.bind(this)); 
					$('#add-config-modal').find('.modal-title').html(dialog_title);	
		    		$('#add-config-modal').modal('toggle');
		    		return this;
				},
				submit: function(){
			    	var post_data 		= $('#add-config-modal').find('#add-config-modal-form').serializeJSON();
			    	post_data['config-type'] = $('#add-config-modal').find('#add-config-modal-form').find('#config-type').val();
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
				},
				confirmremove: function() {
					var ev = arguments[0];					
					var el = $(ev.target);
					var idx = el.data('idx');
					var item = this.build_list[idx];
					$('#remove-config-modal').find('#remove-config-modal-submit').off("click");
					$('#remove-config-modal').find('#remove-id').val(item.Id);
					$('#remove-config-modal').find('#remove-config-modal-text').html('ID: ' + item.Id + ', Name: ' + item.Name + ', Version: ' + item.Version);
					$('#remove-config-modal').find('#remove-config-modal-submit').click(function(){
				    	_app.run('config.remove', function(){
				    		$('#remove-config-modal').modal('toggle'); 	
				    		_app.run('config.list');	
				    	});
				    });	
					$('#remove-config-modal').modal('toggle');
		    		return this;
				},
				remove: function(){
					var post_data 		= $('#remove-config-modal').find('#remove-config-modal-form').serializeJSON();
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
						_app.run('authmodal', function(){
				    	});
	            	}
			  	}.bind(this))
			  	.fail(function() {
					this.notify('Request failed', 0);
					_app.run('authmodal', function(){
			    	});
			  	}.bind(this))
			  	.always(function() {
			  	}.bind(this));
    		},
    		authmodal: function(){
    			$('#auth-modal').find('#auth-modal-submit').off("click");
				$('#auth-modal').find('#auth-modal-submit').click(function(){
					$('#auth-modal').modal('toggle');
			    });	
    			$('#auth-modal').modal('toggle');
    			return this;
    		}
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

	    this.init = function() {
	    	$('#builds-tab-nav').click(function(){
	    		$('#tab-content > div').hide();
	    		$('#builds-tab').show();
   				_app.run('build.list');
	    	});
	    	$('#mailinglist-tab-nav').click(function(){
	    		$('#tab-content > div').hide();
	    		$('#mailinglist-tab').show();
   				_app.run('maillist.list');
	    	});
	    	$('#preference-tab-nav').click(function(){
	    		$('#tab-content > div').hide();
	    		$('#preference-tab').show();
	    		_app.run('config.list');
	    	});

		    $('#button-add-build').click(function(){
		    	_app.run('auth', function(){
		    		_app.run('build.openform');
		    	});
		    })
		    $('#button-add-email').click(function(){
		    	_app.run('auth', function(){
		    		_app.run('maillist.openform');
		    	});
		    })
		    $('#button-add-config').click(function(){
		    	_app.run('auth', function(){
		    		_app.run('config.openform');
		    	});
		    })
		    $('.dropdown-menu li').on('click', function(){
		    	$('#config-type-dropdown').val($(this).find('a').html());
		    	$('#config-type-dropdown span:first-child').html('Type: ' + $(this).find('a').html());
		    	$('#config-type').val($(this).find('a').data('value'));
			});

		    // ---------------
		    // other GUI
		    // ---------------
		    $("#build-file").fileinput({
		        uploadUrl: "action.php", // server upload action
		        uploadExtraData: { action: 'build_upload' },
		        uploadAsync: true,
		        maxFileCount: 1
		    });
		    $("#build-file").on('change', function(event) {
		        $('#build-file').fileinput('upload')
		        $('#build-file').fileinput('disable');
		    });
		    $('#build-file').on('fileuploaded', function(event, data, previewId, index) {
		        $('#build-file').fileinput('enable');
		        var upload = data.response.data.upload;
		        var form = upload.form, 
		            files = upload.files, 
		            extra = upload.extra,
		            response = upload.response, 
		            reader = upload.reader;                               
		        $('#build-url').val(upload.url);
		    }.bind(this));
	    }
	    this.init();
	}

    window._app = new App(); 
    _app.run('build.list');
})