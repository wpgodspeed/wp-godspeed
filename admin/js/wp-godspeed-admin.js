jQuery(document).ready(function($){

	$(function(){
	  $('[data-toggle="popover"]').popover()
	})

	$(function(){
		$(document).on('click', '.notice-registration .notice-dismiss', function(){
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'notice_registration_dismissed'
				}
			})
		})
	})

	$(function(){
		$(document).on('click', '.notice-distribution .notice-dismiss', function(){
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'notice_distribution_dismissed'
				}
			})
		})
	})

	$(function(){

		if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-setup') {
			$.fn.editable.defaults.mode = 'inline';
			$('#email_editable').editable({
				type: 'text',
				send: 'never',
				title: 'Your work email',
				success: function(response, newValue) {
					$('#email').val(newValue);
				}
			});
		}
	})

	$(function(){
		//show/hide divs based on current parameters
		if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-setup'){
			var reg = (ajax_vars.reg == true ? 'contents' : 'none');
			var cdn = (ajax_vars.cdn == true ? 'contents' : 'none');
			var rdy = (ajax_vars.rdy == true ? 'contents' : 'none');
			$('#registration').css('display',  reg);
			$('#distribution').css('display',  cdn);
			$('#setupcomplete').css('display', rdy);
		}

		if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-status'){
			var stats  = (ajax_vars.stats == true ? 'contents' : 'none');
			$('#stats-display').css('display', stats);
		}

	})

	$(function(){

		if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-status'){
			var lab = [];
			var dat = [];
			if (typeof ajax_vars.m6 !== 'undefined'){
				lab.push(ajax_vars.m6.name);
				dat.push(ajax_vars.m6.data);
			}
			if (typeof ajax_vars.m5 !== 'undefined'){
				lab.push(ajax_vars.m5.name);
				dat.push(ajax_vars.m5.data);
			}
			if (typeof ajax_vars.m4 !== 'undefined'){
				lab.push(ajax_vars.m4.name);
				dat.push(ajax_vars.m4.data);
			}
			if (typeof ajax_vars.m3 !== 'undefined'){
				lab.push(ajax_vars.m3.name);
				dat.push(ajax_vars.m3.data);
			}
			if (typeof ajax_vars.m2 !== 'undefined'){
				lab.push(ajax_vars.m2.name);
				dat.push(ajax_vars.m2.data);
			}
			if (typeof ajax_vars.m1 !== 'undefined'){
				lab.push(ajax_vars.m1.name);
				dat.push(ajax_vars.m1.data);
			}

			var ctx   = document.getElementById('myChart').getContext('2d');
			var chart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: lab,
					datasets: [{
						label: "Monthly Usage in GB",
						borderColor: 'rgb(53, 149, 225)',
						fill: false,
						data: dat,
						borderWidth: 3,
						tension: 0.2
					}]
				},
				options: {
					legend: {
						display: false
					},
					layout: {
						padding: {
							//left: 0,
							//right: 0,
							//top: 0,
							//bottom: 10
						}
					}
				}
			});
		}

	})

	var createClicked;
	function checkDist(){

		if (ajax_vars.cdn_processing == 1 || createClicked == true){
			if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-setup'){
				$('#submit_create_dist').prop('disabled', true);
				$('#submit_create_dist').html("<i class='fa fa-gear fa-spin' style='color:#ffffff !important;'></i> Preparing the CDN");
			}

			setTimeout(function(){

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'wpgods_get_dist_state'
					},
					success: function(result){
						if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-setup' && result.processing == false){
							//$('.cdn_id').html(result.cdn_id);
							//$('.cdn_uid').html(result.cdn_uid);
							//$('.cdn_alias').html(result.cdn_alias);
							//$('#distribution').hide();
							//$('#setupcomplete').show();
							//createClicked = false;
							//console.log('done');
							window.location.reload();
						}
						$('#dist_result').css('display', 'block');
						//$('#dist_result').html("<p>In progress for " + result.waiting + " minutes, please stand by.</p>");
						var pct = Math.floor(Number(result.waiting).toFixed(2)/35*100);
						$('.progress-bar').css('width', pct + '%');
						$('.progress-bar').html(pct + '%');
						console.log('wp godspeed long polling cdn status');
						console.log(result);
					},
					error: function(result){
						console.log(result);
					}
				});
				checkDist();
			}, 10000);
		}
	}
	checkDist();

	$(function(){

		$('#sync').on('click', function(){

			$('#sync').html('Syncing');
			//var token = document.getElementById("token").getAttribute("value");
			var token = $('#token').attr('value');

			$.ajax({
				url: 'https://api.godspeedcdn.com/usage/stats',
				type: 'POST',
				beforeSend: function(request){
					request.setRequestHeader('Authorization', 'Bearer ' + token);
				},
				dataType: 'json',
				data: null,
				success: function(result){
					console.log(result);
					if (result.result == "success"){

						$.ajax({
							url: ajaxurl,
							type: 'POST',
							dataType: 'json',
							data: {
								action: 'wpgods_dist_update_stats_cb',
								data: result
							},
							success: function(result){
								//window.location.reload();
							},
							error: function(result){
								console.log(result);
							}
						})
						if (result.plan_data !== null){
							var dr = Number(result.plan_data).toFixed(2);
						} else {
							var dr = $('#dr').html();
						}

						var du  = $('#du').html();
						dr      = dr.slice(0, -2);
						du      = du.slice(0, -2);
						dr      = Number(dr).toFixed(2);
						du      = Number(du).toFixed(2);
						td      = +du + +dr;
						var du2 = Number(result.data_usage).toFixed(2);
						var dr2 = +td - +du2;
						dr2     = Number(dr2).toFixed(2);
						$('#du').html(du2 + 'GB');
						$('#dr').html(result.data_remaining + 'GB');
						$('#pn').html(result.name);
						var pct = du2 / result.plan_data * 100;
						pct     = Number(pct).toFixed(0);
						$('.progress-bar').html(pct + '%');
						$('.progress-bar').css('width', pct + '%');
						if (result.cdn_status == 'disabled'){
							$('#st').removeClass('dataitem');
							$('#st').addClass('dataitem-warn');
						}
						if (result.cdn_status == 'active'){
							$('#st').removeClass('dataitem-warn');
							$('#st').addClass('dataitem');
						}
						$('#st').html(result.cdn_status);
						setTimeout(function () { $('#sync').html('Sync'); }, 500);
					}
				},
				error: function(xhr, resp, text){
					//var json = $.parseJSON(text);
					//console.log(result);
					//console.log(json);
					console.log('failed to make api request');
					console.log(xhr, resp, text);
				}
			})
		});
	});

	$(function(){

		$("#submit_create_dist").on('click', function(){

			if (ajax_vars.hook == 'wp-godspeed_page_wp-godspeed-setup'){
					$('#submit_create_dist').prop('disabled', true);
					$('#submit_create_dist').html("<i class='fa fa-spinner fa-spin' style='color:#ffffff !important;'></i> Creating distribution");
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				/* beforeSend: function(request){
					request.setRequestHeader('Authorization', 'Bearer ' + token);
				}, */
				dataType: 'json',
				data: {
					data: null,
					action: 'wpgods_create_dist'
				},
				success: function(result){
					createClicked = true;
					checkDist();
					console.log(result.result);
				},
				error: function(xhr, resp, text){
					$('#error_dist').html('Failed to make API request during createDistribution');
					$('#error_dist').css('display', 'block');
					var json = $.parseJSON(text);
					console.log('failed to make api request');
					console.log(xhr, resp, text);
					console.log(json);
				}
			})
		});
	});

	$(function(){

		$("#submit").on('click', function(){

			var rpostData = JSON.stringify($('#form').serializeArray());

			$.ajax({
				url: 'https://api.godspeedcdn.com/account/register',
				type: 'POST',
				dataType: 'json',
				data: rpostData,
				success: function(result){

					$.ajax({
						url: ajaxurl,
						async: false,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'registration_callback',
							data: result
						},
						success: function(res){
							console.log('registration_callback success');
							window.location.reload();
						},
						error: function(res){
							console.log('registration_callback failed');
							console.log(res);
							window.location.reload();
						}
					})

				},
				error: function(xhr, resp, text){
					$('#error_reg').html('Failed to make API request for registration');
					$('#error_reg').css('display', 'block');
					console.log('failed to make api request');
					console.log(xhr, resp, text);
				}
			})
		});
	});

	$('.btn-deactivated-cdn').click(function(){
		$(this).button('toggle');
		$('#act').removeClass('focus');
		$('.btn-activated-cdn').removeClass('btn-primary');
		$('.btn-activated-cdn').addClass('btn-secondary');
		console.log('cdn toggled to off');
		var status = 0;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'wpgods_cdn_toggle_cb',
				data: status
			},
			success: function(result){
				$('#godspeedcdn-deactivated').modal('show');
			}
		}).fail(function(result){
			console.log(result);
		});
	});

	$('.btn-activated-cdn').click(function(){
		$(this).button('toggle');
		$(this).removeClass('btn-secondary');
		$(this).addClass('btn-primary');
		$('#dea').removeClass('focus');
		console.log('cdn toggled to on');
		var status = 1;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'wpgods_cdn_toggle_cb',
				data: status
			},
			success: function(result){
				if (result.result == 'precheckfailed'){
					$('#godspeedcdn-activation-failed-error').html('Error code ' + result.error_code);
					$('#godspeedcdn-activation-failed').modal('show');
					$('.btn-activated-cdn').removeClass('btn-primary');
					$('.btn-activated-cdn').removeClass('active');
					$('.btn-deactivated-cdn').addClass('active');
					$('.btn-deactivated-cdn').addClass('focus');
					$('.btn-activated-cdn').addClass('btn-secondary');
					console.log('pre-check failed: cdn not enabled');
				}
				if (result.result == 'precheckpassed'){
					$('#godspeedcdn-activated').modal('show');
					console.log('pre-check passed: cdn enabled');
				}
			}
		}).fail(function(result){
				console.log(result);
		});
	});

	$('.btn-lazyload-img').click(function(){
		var pressed = $(this).attr('aria-pressed');
		if (pressed == 'false'){
			status = 0;
			$(this).removeClass('btn-primary');
			$(this).addClass('btn-secondary');
			console.log('lazy images disabled');
		}
		if (pressed == 'true'){
			status = 1;
			$(this).removeClass('btn-secondary');
			$(this).addClass('btn-primary');
			$(this).removeClass('focus');
			console.log('lazy images enabled');
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'wpgods_lazyload_toggle_cb',
				data: {status: status, btn: 'img'}
			},
			success: function(result){
				console.log(result);
			}
		}).fail(function(result){
				console.log(result);
		});
	});

	$('.btn-lazyload-if').click(function(){
		var pressed = $(this).attr('aria-pressed');
		if (pressed == 'false'){
			status = 0;
			$(this).removeClass('btn-primary');
			$(this).addClass('btn-secondary');
			$('.btn-lazyload-yt').removeClass('btn-primary focus');
			$('.btn-lazyload-yt').addClass('btn-secondary');
			console.log('lazy iframes disabled');
		}
		if (pressed == 'true'){
			status = 1;
			$(this).removeClass('btn-secondary');
			$(this).addClass('btn-primary');
			$(this).removeClass('focus');
			console.log('lazy iframes enabled');
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'wpgods_lazyload_toggle_cb',
				data: {status: status, btn: 'if'}
			},
			success: function(result){
				console.log(result);
			}
		}).fail(function(result){
				console.log(result);
		});
	});

	$('.btn-lazyload-yt').click(function(){
		var pressed = $(this).attr('aria-pressed');
		if (pressed == 'false'){
			status = 0;
			$(this).removeClass('btn-primary');
			$(this).addClass('btn-secondary');
			console.log('lazy youtube disabled');
		}
		if (pressed == 'true'){
			status = 1;
			$(this).removeClass('btn-secondary');
			$(this).addClass('btn-primary');
			$(this).removeClass('focus');
			$('.btn-lazyload-if').removeClass('btn-secondary');
			$('.btn-lazyload-if').addClass('btn-primary');
			console.log('lazy youtube enabled');
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'wpgods_lazyload_toggle_cb',
				data: {status: status, btn: 'yt'}
			},
			success: function(result){
				console.log(result);
			}
		}).fail(function(result){
				console.log(result);
		});
	});

});
