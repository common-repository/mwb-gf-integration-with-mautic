jQuery( document ).ready( function() {

	const crm          = mwb_gf_ajax_data.crm
	const ajaxUrl      = mwb_gf_ajax_data.ajaxUrl;
	const nonce        = mwb_gf_ajax_data.ajaxNonce;
	const action       = mwb_gf_ajax_data.ajaxAction;
	const error        = mwb_gf_ajax_data.criticalError;
	const feedBackLink = mwb_gf_ajax_data.feedBackLink;
	const feedBackText = mwb_gf_ajax_data.feedBackText;
	const isPage       = mwb_gf_ajax_data.isPage;
	const trashIcon    = mwb_gf_ajax_data.trashIcon;
	const apiKeyImg    = mwb_gf_ajax_data.apiKeyImg;
	const webtoImg     = mwb_gf_ajax_data.webtoImg;
	const adminUrl     = mwb_gf_ajax_data.adminUrl;

	let feedFilters;
	let formFields;
	let intMethod = 'basic';

	/**==================================================
            		Liberary Functions.
    ====================================================*/
	/**
	 * Initiliase the Table module.
	 *
	 * The function renders logs table on tab screen.
	 *
	 * @since      1.0.0
	 * @access     public
	 */
	const enableDataTable = () => {

		let ajax_url = jQuery( '#mwb-' + crm + '-gf-logs' ).attr( 'ajax_url' );
		ajax_url     = ajax_url + '?action=get_datatable_logs';

		jQuery( '#mwb-' + crm + '-gf-table' ).DataTable( {
			"processing": true,
			"serverSide": true,
			"ajax"      : ajax_url,
			"scrollX"   : true,
			"dom"       : 'tr<"bottom"ilp>', // extentions position
			"ordering"  : false, // enable ordering
			"pagination": false,
			responsive : {
              details: {
                type: 'column'
            }
        },

         columnDefs: [ {
            className: 'dtr-control',
            orderable: false,
            targets:   0
        } ],
         order: [ 1, 'asc' ],
	
			language    : {
				"lengthMenu": "Rows per page _MENU_",
				"info"      : "",
			}
		} );
	}

	/**
	 * Check if pgae is logs page and display datatable.
	 *
	 * @since     1.0.0
	 * @returns   void
	 */
	const isLogsPage = () => {
		if ( 'logs' == isPage ) {
			enableDataTable();
		}
	}

	/**
	 * Define default feed title.
	 * 
	 * @since     1.0.0
	 * @returns   void
	 */
	const defaultFeedTitle = () => {

		if ( jQuery( 'body' ).hasClass( 'post-type-mwb_' + crm + '_gf' ) ) {
			let feed_title = jQuery( 'input[name="post_title"]' ).val();
			let feed_id    = jQuery( 'input[name="post_ID"]' ).val();
			if ( ! feed_title ) {
				jQuery( 'input[name="post_title"]' ).val( 'Feed ' + '#' + feed_id );
			}
		}
	}
	
	/**
	 * Hide visibility options.
	 * 
	 * @since     1.0.0
	 * @returns   void
	 */
	const hideFeedEditOptions = () => {

		if ( jQuery( 'body' ).hasClass( 'post-type-mwb_' + crm + '_gf' ) ) {
			jQuery( 'div#visibility.misc-pub-section.misc-pub-visibility' ).css( 'display', 'none' );
			jQuery( 'div#delete-action' ).css( 'display', 'none' );
		}

		/* Display filters metabox only if valid gf form */
		let form = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();
		if ( '-1' == form ) {
			jQuery( '#mwb_' + crm + '_gf_feeds_condition_meta_box' ).slideUp();
		} else {
			jQuery( '#mwb_' + crm + '_gf_feeds_condition_meta_box' ).slideDown();
		}

	} 
	
	/**
	 * Display back to feeds link on edit page.
	 * 
	 * @param     {string} link Link to the page.
	 * @param     {string} text Text to appear on link.
	 * @since     1.0.0 
	 * @returns   void
	 */
	const backToFeeds = ( link, text ) => {
		if ( jQuery( 'body' ).hasClass( 'post-type-mwb_' + crm + '_gf' ) ) {
			jQuery( '.page-title-action' ).after( '<a class="page-title-action" href="' + link + '">' + text + '</a>' );
		}
	}

	/**
	 * Add data to the current query.
	 *
	 * @param     {string} uri      Query string.
	 * @param     {string} key      Key to add to query.
	 * @param     {string} value    Value of the key to add to query.
	 * @since     1.0.0 
	 * @returns   updated query.
	 */
	const updateQueryStringParameter = ( uri, key, value ) => {
		let re        = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
		let separator = uri.indexOf( '?' ) !== -1 ? "&" : "?";

		if ( uri.match( re ) ) {
			return uri.replace( re, '$1' + key + "=" + value + '$2' );
		} else {
			return uri + separator + key + "=" + value;
		}
	}

	/**
	 * Ajax request callback.
	 * 
	 * @param     {array} args An array of ajax arguments.
	 * @returns   object
	 * @since     1.0.0
	 */
	async function doAjax( args ) {

		try{
			return await jQuery.ajax( {
				url  : ajaxUrl,
				type : 'POST',
				data : args,
				beforeSend: function() {
					
				},
			} );

		} catch ( error ) {

			console.error( error );
		}
	}
	
	/**
	 * Success alert via sweet alert-2
	 * 
	 * @param       {string} msg Message to alert.
	 * @returns     void
	 * @since       1.0.0
	 */
	const triggerSuccess = ( msg = '' ) => {
		Swal.fire( {
			icon : 'success',
			title: 'Process Complete',
			text : msg,
			timer: 1500,
		} );
	}
	
	/**
	 * Error alert via sweet alert-2.
	 * 
	 * @param      {string} msg Message to alert.
	 * @returns    void
	 * @since      1.0.0
	 */
	const triggerError = ( msg = 'Something went wrong' ) => {
		Swal.fire( {
			icon : 'error',
			title: 'Opps...!!',
			text : msg,
		} );
	}
	
	/**
	 * Info alert via sweet alert-2.
	 * 
	 * @param        {string} msg Message to alert.
	 * @since        1.0.0
	 * @returns      void
	 */
	const triggerInfo = ( msg = '' ) => {
		Swal.fire( {
			icon  : 'info',
			title : 'Alert',
			text  : msg,
		} );
	}

    /**
	 * Create tooltips via tiptip js.
	 * 
	 * @returns      void.
	 * @since        1.0.0
	 */
	const display_tooltip = () => {

		let args = {
			'attribute'      : 'data-tip',
			'delay'          : 200,
			'defaultPosition': 'bottom',
		};

		jQuery( '.mwb_' + crm + '_gf_tips' ).tipTip( args );
  	}

	/**
	 * Trigger setup docs.
	 * 
	 * @since   1.0.1
	 * @return  void
	 */
	 const triggerSetupDocs = (intMethod) => {
		switch( intMethod ) {
			case 'oauth2':
				Swal.fire( {
					title: 'How to get the API keys?',
					html:
					'<ul class="mwb_list_points" id="instructions_points_alignment">' +
					'<li>Go to Mautic Configuration / API Settings and set "API enabled" to "Yes".</li>' +
					'<li>Then click on setting icon and go to Api Credentials</li>' +
					'<li>Then Select OAuth2, and click on "New" on top right corner.</li>' +
					'<li>Enter any name in app name section and enter.</li>' +
					'<li>Enter ' + adminUrl + ' callback URL</li>' +
					'<li>click on Apply on top right corner.</li>' +
					'<li>Save app, Copy Client Id & Client Secret Key.</li>' +
					'</ul>' + 
					'<img class="instruction_image" src="' + apiKeyImg + '">',
					allowOutsideClick : false,
				} );
				break;
			
			case 'basic':
				Swal.fire( {
					title: 'No need of Api keys in basic auth.',
					html:
					'<ul class="mwb_list_points">' +
					'<li>In basic authentication, you just need username and password of mautic from which you logged in to mautic.</li>' +
					'</ul>' + 
					'<img class="instruction_image" src="' + webtoImg + '">',
					allowOutsideClick : false,
				} );
				break;
		}
	}


	/**
	 * Update a select elemnet
	 * 
	 * @param      {string} selectId       ID of select element
	 * @param      {object} selectOptions  Options to update.
	 * @since      1.0.0
	 * @returns    html
	 */
	const updateSelectOptions = ( selectId, selectOptions ) => {
	 
		let	html = '<option value="-1">Select an Object</option>';
	
		jQuery.each( selectOptions, function ( index, value ) {
			html += '<option value="' + index + '">'+ value + '</option>';
		});
	
		jQuery( '#' + selectId ).html( html );
	}

	/**
	 * Update selected element in add new field.
	 * 
	 * @param     {string} select   Select field object.
	 * @param     {string} value    Selected value
	 * @param     {string} disable  Whether to disable to field or not.
	 * @since     1.0.0
	 * @returns   void
	 */
	const updateLastSelectedOption = (select, value, disable) => {

		let allOptions = select.find( 'option' );

		jQuery.each( allOptions, function () { 
			if ( value == jQuery( this ).attr( 'value' ) ) {
				jQuery( this ).attr( 'disabled', disable );
			}
		} );
		select.prop( 'selectedIndex', 0 );
	}

	/**
	 * Update Primary field options
	 * 
	 * @param      {string} key     Option key
	 * @param      {string} value   Option value.
	 * @param      {string} action  Action to perform
	 * @since      1.0.0
	 * @returns    void
	 */
	const updatePrimaryField = ( key, value, action ) => {

		if ( 0 == key.length ) {
			return;
		}

		if ( 'add' == action ) {
			let option = '<option value="' + key + '">' + value + '</option>';
			jQuery( '#primary-field-select' ).append( option );

		} else if ( 'remove' == action ) {
			jQuery.each( jQuery( '#primary-field-select' ).find( 'option' ), function() {
				if ( key == jQuery( this ).attr( 'value' ) ) {
					jQuery( this ).remove();
				}
			} );
		}

	}

	/**
	 * Load feed form on page load.
	 * 
	 * @since      1.0.0
	 * @returns    void
	 */
	const initiliazeFeedForm = () => {

		let form        = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();
		let object      = jQuery( '#mwb-feeds-' + crm + '-object' ).val();
		let post_id     = jQuery('#post_ID').val();
		let post_type   = jQuery('#post_type').val();
		let post_status = jQuery('#original_post_status').val();

		if ( '-1' != object && '-1' != form && '' != post_id && 'mwb_' + crm + '_gf' == post_type ) {
			if ( 'publish' == post_status ) {
				let event_mapping = 'get_object_fields_for_mapping';
				let event_field   = 'create_feed_filters';

				let data  = {
					action          : action,
					nonce           : nonce,
					event           : event_mapping,
					post_id         : post_id,
					selected_object : object,
					selected_form   : form,	
				};

				let input = {
					action        : action,
					nonce         : nonce,
					event         : event_field,
					selected_form : form,
				} ;

				populateFeedFrom( data );
				populateFilterForm( input );
			} else {
				jQuery( '#mwb-feeds-' + crm + '-object' ).trigger( 'change' );
			}
		}
	}
	
	/**
	 * Populate feeds form.
	 * 
	 * @param   {object} data    Feed object.
	 * @since   1.0.0
	 * @returns html
	 */
	const populateFeedFrom = ( data ) => {

		let result = doAjax( data );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( response.data.hasOwnProperty( 'html' ) ) {
					jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').slideUp();
					jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').remove();
					jQuery('.mwb-feed__select-object').closest( 'div.mwb_' + crm + '_gf__feeds-wrap' ).parent().append( response.data.html );
					jQuery('.mwb-content-wrap').slideDown();
				}
			}
		} );
	}

	/**
	 * Populate feeds filter form.
	 * 
	 * @param   {object} data    Feed object.
	 * @since   1.0.0
	 * @returns html
	 */
		 const populateFilterForm = ( data ) => {

			let result = doAjax( data );
			result.then( ( response ) => {
				if ( true == response.status ) {
					formFields  = response.data.form;
					feedFilters = response.data.filter;
				}
			} );
		}

	/**
	 * Reset Feed if not a valid gf form.
	 * 
	 * @since      1.0.0
	 * @returns    void.
	 */
	const resetFeedForm = () => {

		jQuery( '#mwb-feeds-' + crm + '-object' ).prop( 'selectedIndex', 0 );
		jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').slideUp();
		jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').remove();
		jQuery( '#mwb_' + crm + '_gf_feeds_condition_meta_box' ).slideUp();
	}

	/**
	 * Create Or filtered Html.
	 * 
	 * @param      {integer} index       Next or index.
	 * @returns    html
	 * @since      1.0.0
	 */
	 const createOrFilteredHtml = ( index = 1 ) => {

		let html = '';
		html += '<div class="or-condition-filter" data-or-index="' + index + '">';
		html += '<div class="mwb-form-filter-row">';
		html +=	'<div class="and-condition-filter" data-or-index="' + index + '" data-and-index="1">';	
		html += '<select name="condition[' + index + '][1][field]" class="condition-form-field">';
		html += '<option value="-1">Select Field</option>';

		jQuery.each( formFields, function ( index, value ) { 
			html += '<optgroup label="' + index + '">';
			jQuery.each( value, function ( i, v ) { 
				html += '<option value="' + i + '" >' + v + '</option>';
			} );
			html += '</optgroup>';
		} );

		html += '</select>';
		html += '<select name="condition[' + index + '][1][option]" class="condition-option-field">';
		html += '<option value="-1">Select Condition</option>';

		jQuery.each( feedFilters, function( _index, _value )  {
			html += '<option value=' + _index + '>' + _value + '</option>';
		} );
			
		html += '</select>';
		html += '<input type="text" name="condition[' + index + '][1][value]" class="condition-value-field" value="" placeholder="Enter value">';
		html += '</div>';

		if ( 1 != index ) {
			html += '<img src="' + trashIcon + '" class="dashicons-trash" alt="Trash" id="condition_trash_new">';
			
		}
		
		html += '<button data-next-and-index="2" data-or-index="' + index + '" class="button condition-and-btn">Add "AND" filter</button>';
		html += '</div></div>';

		return html;

	}

	/**
	 * Create And filtered Html
	 * 
	 * @param      {integer} index   Next and Index.
	 * @param      {integer} orIndex Next or Index
	 * @returns    html
	 * @since      1.0.0
	 */
	const createAndFilteredHtml = ( index = 1, orIndex = 1 ) => {

		let html = '';

		html +=	'<div class="and-condition-filter" data-and-index="' + index + '" data-or-index="' + orIndex + '">';	
		html += '<select name="condition[' + orIndex + '][' + index + '][field]" class="condition-form-field">';
		html += '<option value="-1">Select Field</option>';

		jQuery.each( formFields, function ( index, value ) { 
			html += '<optgroup label="' + index + '">';
			jQuery.each( value, function ( i, v ) { 
				html += '<option value="' + i + '" >' + v + '</option>';
			} );
			html += '</optgroup>';
		} );

		html +='</select>';
		html += '<select name="condition[' + orIndex + '][' + index + '][option]" class="condition-option-field">';
		html += '<option value="-1">Select Condition</option>';

		jQuery.each( feedFilters, function( _index, _value )  {
			html += '<option value=' + _index + '>' + _value + '</option>';
		} );
			
		html += '</select>';
		html += '<input type="text" name="condition[' + orIndex + '][' + index + '][value]" class="condition-value-field" value="" placeholder="Enter value">';
		if ( 1 !=  index ) {
			html += '<span class="dashicons dashicons-no"></span>';
		}
		html += '</div>';

		return html;
	}

	/**
	 * Disable filter feilds on page load.
	 * 
	 * @since      1.0.0
	 * @returns    void
	 */ 
	 const disableFilterFields = () => {
		jQuery( '.condition-option-field  option:selected' ).each( function() {
			let condition = jQuery( this ).val();
			let value     = jQuery( this ).next( '.condition-value-field' );
			if ( 'empty' == condition || 'not_empty' == condition ) {
				jQuery( this ).closest( '.and-condition-filter' ).find( '.condition-value-field' ).val( '' ).attr( 'disabled', 'disabled' ).removeAttr( 'placeholder' );
			}
		} );
	}

	/**
	 * Select2 implementation.
	 * 
	 * @since 1.0.1
	 * @return void
	 */
	 const applySelect2 = () => {
		if ( jQuery( 'body' ).hasClass( 'forms_page_mwb_' + crm + '_gf_page' ) ) {
		}
	}

	/**
	 * Create add note fields.
	 * 
	 * @since    1.0.0
	 * @return   html.
	 */
	 const createAddNoteField = () => {

		if ( jQuery( '#mwb_' + crm + '_gf_feeds_note_meta_box' ).length == 0 ) {
			return;
		}

		let html = '';

		html +=	'<textarea name="add_feed_note" id="add-feed-note" cols="50" rows="2"></textarea>';
		html += '<div class="mwb_info">Choose form fields<code>{ field_id } </code>form the following form fields to add as note.</div>';
		html += '<select name="add-note-select" id="add-note-select">';
		html += '<option value="-1">Select field</option>'
		
		jQuery.each( formFields, function ( index, value ) { 
			html += '<optgroup label="' + index + '">';
			jQuery.each( value, function ( i, v ) { 
				html += '<option value="' + i + '" >' + v + '</option>';
			} );
			html += '</optgroup>';
		} );

		html += '</select>';

		return html;

	}

	/**==================================================
            	"on-load" function called here.
    ====================================================*/

	/* Load feeds form on page load */
	initiliazeFeedForm();

	/* Display tooltips */
	display_tooltip();

	/* Display back to feeds link */
	backToFeeds( feedBackLink, feedBackText );

	/* Default feed title */
	defaultFeedTitle();

	/* Hide visibility and trash option on feed edit page */
	hideFeedEditOptions();

	/* Display datatable */
	isLogsPage();

	/* Disable filter fields */
	disableFilterFields();

	/* Apply select2 */
	applySelect2();

	/**==================================================
                	Settings Section JS.
    ====================================================*/

	/* Display email field on enable */
    jQuery( 'input[name="mwb_setting[enable_notif]"]' ).on( 'change', function() {
        
        let email_enable = jQuery( this ).is( ':checked' );
        if ( email_enable ) {
            jQuery( '#mwb_' + crm + '_gf_email_notif' ).removeClass( 'is_hidden' );
  
        } else {
            jQuery( '#mwb_' + crm + '_gf_email_notif' ).addClass( 'is_hidden' );
        }
    } );
	
	jQuery( '#mwb-mautic_gf-auth_type' ).on( 'change', function() {
        
        let auth_type = jQuery( this ).val();
        if ( auth_type == "basic" ) {
			jQuery( '#row-hide-clientid' ).hide();
			jQuery( '#row-hide-secretid' ).hide();
			jQuery( '#row-hide-username' ).show();
			jQuery( '#row-hide-password' ).show();
        } else if( auth_type == "oauth2" ) {
			jQuery( '#row-hide-clientid' ).show();
			jQuery( '#row-hide-secretid' ).show();
			jQuery( '#row-hide-username' ).hide();
			jQuery( '#row-hide-password' ).hide();
        } else {
			jQuery( '#row-hide-clientid' ).hide();
			jQuery( '#row-hide-secretid' ).hide();
			jQuery( '#row-hide-username' ).hide();
			jQuery( '#row-hide-password' ).hide();
		}
    } );

	jQuery( document ).on( 'click', '.edit-post-status', function() {
		jQuery( '#post_status' ).html('<option selected="selected" value="publish">Published</option><option value="draft">Draft</option>');
	} );

	jQuery( window ).on( 'load', function() {
		jQuery( '.is_hidden_accounts_tab' ).attr( 'style', 'display:none' );
    } );

	/* Validations on imput type number */
	jQuery( 'input[name="mwb_setting[delete_logs]"]' ).on( 'keyup change focusout', function() {

		if ( jQuery( this ).val() < 0 ) {
			jQuery( this ).val( 0 );
		}

		if ( jQuery( this ).val() % 1 != 0 ) {
			let value  = jQuery( this ).val();
			value      = value.toString();
			let length = value.length;

			if ( length > 4 ) {
				triggerError( 'Value must be Integer' );
			}
		}
	} );

    /**==================================================
                	Accounts Section JS.
    ====================================================*/

	/* Show auth form */
	jQuery( '#mwb-showauth-form' ).on( 'click', function( e ) {
		e.preventDefault();
		jQuery( '.mwb-intro' ).slideUp( '400', function() {
			jQuery( '.mwb_sf_gf__account-wrap' ).slideDown();
		} );
	} );

	/* Show setup documentation */
	jQuery( document ).on( 'click', '.trigger-setup-guide', function() {
		var intMethod = jQuery( '#mwb-mautic_gf-auth_type' ).val();
		triggerSetupDocs( intMethod );
	} );

	// Connection status toogle /
	jQuery( '.mwb-section__sub-heading__wrap' ).on( 'click', function() {
		jQuery( this ).toggleClass( 'mwb-slide' );
		jQuery( this ).siblings( '.mwb-sf_gf__status-wrap' ).slideToggle( 300 );
	} );

    /* Toggle input type */
    jQuery( '.mwb-toggle-view' ).on( 'click', function( e ) {
        e.preventDefault();

        let input = jQuery( this ).parent().siblings( 'input' );

        if ( jQuery( this ). hasClass( 'dashicons-visibility' ) ) {
            input.attr( 'type', 'text' );
            jQuery( this ).removeClass( 'dashicons-visibility' ).addClass( 'dashicons-hidden' );
        } else {
            input.attr( 'type', 'password' );
            jQuery( this ).removeClass( 'dashicons-hidden' ).addClass( 'dashicons-visibility' );
        }
    } );

	/* Authorize mautic account */
	jQuery( '#mwb-' + crm + '_gf-authorize-button' ).on( 'click', function( e ) {
		e.preventDefault();
		
		href            = jQuery( this ).attr( 'href' );
		consumer_key    = jQuery( '#mwb-' + crm + '_gf-consumer-key' ).val();
		consumer_secret = jQuery( '#mwb-' + crm + '_gf-consumer-secret' ).val();
		auth_type       = jQuery( '#mwb-' + crm + '_gf-auth_type' ).val();
		redirect_url    = jQuery( '#mwb-' + crm + '_gf-redirect-url' ).val();
		username        = jQuery( '#mwb-' + crm + '_gf-username' ).val();
		password        = jQuery( '#mwb-' + crm + '_gf-password' ).val();
		
		if( auth_type == 'oauth2' ) {
			if ( consumer_key.length == 0 || consumer_secret.length == 0 ) {
				triggerError( 'All fields are required.' );
			} else {
				if( ! redirect_url ) {
					triggerError( 'All fields are required.' );
				} else {
					href = updateQueryStringParameter( href, 'consumer_key', consumer_key );
					href = updateQueryStringParameter( href, 'consumer_secret', consumer_secret );
					href = updateQueryStringParameter( href, 'auth_type', auth_type );
					href = updateQueryStringParameter( href, 'redirect_uri', redirect_url );
		
					jQuery( this ).attr( 'href', href );
					window.location.href = href; // Now Redirect.
				}
			}
		} else if( auth_type == 'basic' ) {
			if ( username.length == 0 || password.length == 0 ) {
				triggerError( 'All fields are required.' );
			} else {
				if( ! redirect_url ) {
					triggerError( 'All fields are required.' );
				} else {

					let event = 'check_basic_auth_credentials';
					let input = {
						action : action,
						nonce  : nonce,
						event  : event,
						username : username,
						password : password,
					};

					let result = doAjax( input );
					result.then( ( response ) => {
						if( response !== 'Success' ) {
							triggerError( response );

							href = updateQueryStringParameter( href, 'auth_type', auth_type );
							href = updateQueryStringParameter( href, 'redirect_uri', redirect_url );
							href = updateQueryStringParameter( href, 'username', username );
							href = updateQueryStringParameter( href, 'password', password );
				
							jQuery( this ).attr( 'href', href );
							setTimeout(function(){ window.location.href = href; }, 3000);
						} else {

							href = updateQueryStringParameter( href, 'auth_type', auth_type );
							href = updateQueryStringParameter( href, 'redirect_uri', redirect_url );
							href = updateQueryStringParameter( href, 'username', username );
							href = updateQueryStringParameter( href, 'password', password );
				
							jQuery( this ).attr( 'href', href );
							window.location.href = href;
						}
					} );
				}
			}
		} else {
			triggerError( 'All fields are required.' );
		}
	} );

	/* Authroization complete */
	jQuery( '.mwb-onboarding-complete' ).on( 'click', function( e ) { 
		e.preventDefault();
		let event = 'mark_onboarding_complete';
		let input = {
			action : action,
			nonce  : nonce,
			event  : event,
		};

		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( true == response.data.success ) {
					location.reload();
				}
			} else {
				triggerError();
			}
		} );
		
	} );
	

	/* Refresh mautic access token */
	jQuery( '#mwb_' + crm + '_gf_refresh_token' ).on( 'click', function( e ) {

		jQuery( this ).css( 'pointer-events', 'none' );
		let event = 'refresh_' + crm + '_access_token';
		let input = {
			action: action,
			nonce : nonce,
			event : event,
		}
		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( true == response.data.success ) {
					triggerSuccess( 'Access token renewed successfully.' );
					jQuery( '#mwb-' + crm + '_gf-token-notice' ).text( response.data.connection_message );
					jQuery( '#mwb-' + crm + '_gf-token-notice' ).append( '<br>' );
					jQuery( '#mwb-' + crm + '_gf-token-notice' ).append( response.data.token_message );
				}
			} else {
				triggerError( response.data.msg );
			}
		} );
	} );

	/* Reauthorize mautic account */
	jQuery( '#mwb_' + crm + '_gf_reauthorize' ).on( 'click', function( e ) {
		e.preventDefault();

		redirect = jQuery( this ).attr( 'href' );
		
		if ( 0 == redirect.length ) {
			triggerError( 'Something went wrong!! Try reloading the page' )
		} else{
			window.location.href = redirect;
		}
	} );

	/* Revoke account access */
	jQuery( '#mwb_' + crm + '_gf_revoke' ).on( 'click', function( e ) {
		e.preventDefault();
		let event = 'revoke_' + crm + '_gf_access';
		let input = {
			action: action,
			nonce : nonce,
			event : event,
		}
		let result = doAjax( input );
		result.then( ( response ) => {
			
			if ( true == response.status ) {
				location.reload();
			} else {
				triggerError( response.message );
			}
		} );
	} );

	/**==================================================
                	Feeds Section JS.
    ====================================================*/

	/* Warning on empty feed title */
	jQuery( 'input[name="post_title"]' ).on( 'keyup', function() {
		let feed_title = jQuery( 'input[name="post_title"]' ).val();
        if ( ! feed_title ) {
			let msg = '<span class="title_warning">*Title field can\'t be empty</span>';
        	jQuery( 'div#titlediv' ).children('div.inside').html( msg );
        } else {
			jQuery( 'div#titlediv' ).children('div.inside').html( '' );
		}
	} );

	/* On from select change */
	jQuery( '#mwb-' + crm + '-gf-select-form' ).on( 'change', function() {

		let form_id = jQuery( this ).val();
		resetFeedForm();

		if ( '-1' == form_id ) {
			resetFeedForm();
			return;
		} else {
			jQuery( '#mwb_' + crm + '_gf_feeds_condition_meta_box' ).slideDown();
		}

		let form  = jQuery( this ).val();
		let event = 'create_feed_filters';
		let input = {
			action        : action,
			nonce         : nonce,
			event         : event,
			selected_form : form,
		} ;

		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {

				// Create filter fields.
				jQuery( '.mwb-initial-filter' ).find( '.or-condition-filter' ).remove();
				formFields  = response.data.form;
				feedFilters = response.data.filter;
				let html = createOrFilteredHtml();
				jQuery( '.condition-or-btn' ).before( html );
				jQuery( '.condition-or-btn' ).attr( 'data-next-or-index', 2 );
				jQuery( '#mwb_' + crm + '_gf_feeds_condition_meta_box' ).slideDown();

				// Create add note field.
				jQuery( '#mwb-' + crm + 'gf-feed-add-note' ).slideUp();
				let note = createAddNoteField();
				jQuery( '#mwb-' + crm + 'gf-feed-add-note' ).html( note );
				jQuery( '#mwb-' + crm + 'gf-feed-add-note' ).slideDown();
			} else {
				triggerError();
			}
		} );
	} );

	/* Get object fields on change */
	jQuery( '#mwb-feeds-' + crm + '-object' ).on( 'change', function() {

		if ( '-1' == jQuery( '#mwb-' + crm + '-gf-select-form' ).val() ) {
			jQuery( this ).prop( 'selectedIndex', 0 );
			triggerInfo( 'Please select a form first' );
			return;
		}

		let module = jQuery( this ).val();
		let form   = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();
		if( module == '-1' ) return;

		let event = 'get_object_fields_for_mapping';
		let input = {
			action          : action,
			nonce           : nonce,
			event           : event,
			selected_object : jQuery.trim( module ),
			selected_form   : form,
		};

		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( response.data.hasOwnProperty( 'html' ) ) {
					jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').slideUp();
					jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').remove();
					jQuery('.mwb-feed__select-object').closest( 'div.mwb_' + crm + '_gf__feeds-wrap' ).parent().append( response.data.html );
					jQuery('.mwb-content-wrap').slideDown();
				}
			}
		} );
	} );

	/* Refresh mautic object */
	jQuery( '#mwb-' + crm + '-refresh-object' ).on( 'click', function() {

		let event     = 'get_crm_objects';
		let select_id = 'mwb-feeds-' + crm + '-object';
		let input = {
			action: action,
			nonce : nonce,
			force : true,
			event : event,
		};
		
		jQuery( this ).text( 'Loading...' );
		jQuery( this ).css( 'pointer-events', 'none' );

		let result = doAjax( input );
		result.then( ( response ) => {

			jQuery( this ).text( 'Refresh Objects' );
			jQuery( this ).css( 'pointer-events', 'auto' );
	
			if ( true == response.status ) {
				if ( response.hasOwnProperty( 'data' ) ) {
					if ( true == response.data.success ) {
						updateSelectOptions( select_id, response.data.data, 'name', 'label', 'object' );
					} else {
						triggerError( response.data.data );
					}
				}
			} else {
				triggerError();
			}
		} );
	} );

	/* Refresh mautic fields */
	jQuery( '#mwb-' + crm + '-refresh-fields' ).on( 'click', function() {

		let module = jQuery( '#mwb-feeds-' + crm + '-object' ).val();
		let form   = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();

		if ( '-1' == module ) {
			triggerError( 'Please select a Mautic Object first' );
			return;
		}

		let event = 'get_object_fields_for_mapping';
		let force = true;
		let input = {
			action          : action,
			nonce           : nonce,
			event           : event,
			force           : force,
			selected_object : module,
			selected_form   : form,
		};

		jQuery( this ).text( 'Loading...' );
		jQuery( this ).css( 'pointer-events', 'none' );
		
		let result = doAjax( input );
		result.then( ( response ) => {

			jQuery( this ).text( 'Refresh Fields' );
			jQuery( this ).css( 'pointer-events', 'auto' );

			if ( true == response.status ) {
				if ( response.data.hasOwnProperty( 'html' ) ) {
					jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').slideUp();
					jQuery('.mwb_' + crm + '_gf__feeds-wrap').nextAll('.mwb-content-wrap').remove();
					jQuery('.mwb-feed__select-object').closest( 'div.mwb_' + crm + '_gf__feeds-wrap' ).parent().append(response.data.html);
					jQuery('.mwb-content-wrap').slideDown();
				}
			}
		} );
	} );

	/* Field type select */
	jQuery( document ).on( 'change', '.field-type-select', function( e ) {

		let type_select = jQuery( this ) ;
		let field_type  = type_select.val(); 
		let wrapper     = type_select.closest( '.mwb-fields-form-row' );

		wrapper.find( '.row-field-value' ).addClass( 'row-hide' );
		wrapper.find( '.row-'+field_type ).removeClass( 'row-hide' );
	} );

	/* Custom value select */
	jQuery( document ).on( 'change', '.custom-value-select', function( e ) {

		let type_select = jQuery( this ) ;
		let field_value = type_select.val(); 
		let wrapper     = type_select.closest( '.mwb-fields-form-row' );
		let input       = wrapper.find( '.custom-value-input' ).val();

		wrapper.find( '.custom-value-input' ).val( input +  '{' + field_value + '}' );
	} );


	/* Add new field in field form */
	jQuery( document ).on( 'click' , '#add-new-field-btn', function( e ) {
		e.preventDefault();

		let select = jQuery( '#add-new-field-select' );
		let field  = select.val();
		let text   = select.find('option:selected').text();
		let object = jQuery( '#mwb-feeds-' + crm + '-object' ).val();
		let form   = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();

		if( '-1' == object ) {
			triggerInfo( 'Select a valid Mautic object and its corresponding field' );
			return;
		} else if( '-1' == field ) {
			triggerInfo( 'Select a Field to add' );
			return;
		}

		let event = 'add_new_field';
		let input = {
			action : action,
			nonce  : nonce,
			event  : event,
			object : object,
			form   : form,
			field  : field,
		};
		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( response.hasOwnProperty( 'data' ) ) {
					if ( true == response.data.success ) {
						updateLastSelectedOption( select, field, true );
						jQuery( '#mwb-fields-form-section' ).append( response.data.html );
						updatePrimaryField( field, text, 'add' );
					} else {
						triggerError( response.data.msg )
					}
				}
			} else {
				triggerError();
			}
		} );
	} );

	/* Trash mapped fields */
	jQuery( document ).on( 'click' , '.mwb-fields-form-row .field-delete', function( e ) {

		let form_row = jQuery( this ).closest( '.mwb-fields-form-row' );
		let field    = form_row.find( '.crm-field-name' ).val();
		let select   = jQuery( '#add-new-field-select' );

		form_row.slideUp( '400', function() {
			updateLastSelectedOption( select, field, false );
			updatePrimaryField( field, '', 'remove' );
			form_row.remove();
		} );
	} );

	/* Add or filter */
	jQuery( document ).on( 'click', '.condition-or-btn', function( e ) {
		e.preventDefault();

		let form = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();
		if ( '-1' == form ) {
			triggerInfo( 'Please select a valid GF form' );
			return;
		}

		let nextOrIndex = jQuery( this ).attr( 'data-next-or-index' );
		let nextOrHtml  = createOrFilteredHtml( nextOrIndex );
		jQuery( this ).before( nextOrHtml );
		nextOrIndex ++;
		jQuery( this ).attr( 'data-next-or-index', nextOrIndex );
	} );

	/* Delete or filter */
	jQuery( document ).on( 'click', 'img.dashicons-trash', function( e )  {
		let row = jQuery( this ).parents( '.or-condition-filter' );
		row.slideUp( '400', function() {
			row.remove();
		} );
	} );

	/* Add and filter */
	jQuery( document ).on( 'click', '.condition-and-btn', function( e ) {
		e.preventDefault();

		let form = jQuery( '#mwb-' + crm + '-gf-select-form' ).val();
		if ( '-1' == form ) {
			triggerInfo( 'Please select a valid GF form' );
			return;
		}

		let nextOrIndex  = jQuery( this ).attr( 'data-or-index' );
		let nextAndIndex = jQuery( this ).attr( 'data-next-and-index' );
		let nextAndHtml  = createAndFilteredHtml( nextAndIndex, nextOrIndex );

		jQuery( this ).before( nextAndHtml );
		nextAndIndex++;
		jQuery( this ).attr( 'data-next-and-index', nextAndIndex );
	} );

	/* Delete and filter */
	jQuery( document ).on( 'click', '.dashicons-no', function( e ) {
		let row = jQuery( this ).parents( '.and-condition-filter' );
		row.slideUp( '400', function() {
			row.remove();
		} );
	} );

	/* Disable filter fields on empty condition */
	jQuery( document ).on( 'change', '.condition-option-field', function() {
	
		let condition = jQuery( this ).val();
		let value = jQuery( this ).next( '.condition-value-field' );
	
		if ( 'empty' == condition || 'not_empty' == condition || 'only_number' == condition || 'only_text' == condition ) {
			value.val( '' ).attr( 'disabled', 'disabled' ).removeAttr( 'placeholder' );
		} else {
			value.val( '' ).removeAttr( 'disabled' ).attr( 'placeholder', 'Enter value' );
		}
	} );

	/* Note field select */
	jQuery( document ).on( 'change', '#add-note-select', function( e ) {
		let type_select = jQuery( this );
		let field_value = type_select.val();
		let input       = jQuery( '#add-feed-note' ).val();

		jQuery( '#add-feed-note' ).val( input + '{' + field_value + '}' );
	} );

	/* Toogle feed status */
	jQuery( '.mwb-feed-status' ).on( 'change', function() {

		let event  = 'toggle_feed_status';
		let feedId = jQuery( this ).attr( 'feed-id' );	

		if ( this.checked ) {	
			let input  = {
				action : action,
				nonce  : nonce,
				event  : event,
				feed_id: feedId,
				status : 'publish',
			};

			let result = doAjax( input );
			result.then( (response ) => {
				if ( true == response.status ) {
					jQuery( '.mwb-feed-status-text_' + feedId ).text( 'Active' );
				}
			} );
	
		} else{
		
			let input = {
				action : action,
				nonce  : nonce,
				event  : event,
				feed_id: feedId,
				status : 'draft',
			};

			let result = doAjax( input );
			result.then( (response ) => {
				if ( true == response.status ) {
					jQuery( '.mwb-feed-status-text_' + feedId ).text( 'Sandbox' );
				}
			} );
		}
	} );

	/* Trash feed from feed list */
	jQuery( '.mwb_' + crm + '_gf__trash_feed' ).on( 'click', function() {
		
		let row     = jQuery( this ).parent().closest( 'li' );
		let feed_id = jQuery( this ).attr( 'feed-id' );
		let event   = 'trash_feeds_from_list';
		let input   = {
			action  : action,
			nonce   : nonce,
			event   : event,
			feed_id : feed_id,
		};

		let result = doAjax( input );
		result.then( ( response ) => {

			if ( true == response.status ) {
				row.slideUp( '400', function() {
					row.remove();
				} );
			} else{
				triggerError( error );
			}
		} );
	} );


	/* Feeds filter */
	jQuery( document ).on( 'change', '.filter-feeds-by-form', function() {

		let form_id = jQuery( this ).val();

		if ( '-1' == form_id ) {
			triggerInfo( 'Select a valid GF form!!' );
			return;
		}

		let event = 'filter_feeds_by_form';
		let input = {
			action  : action,
			nonce   : nonce,
			form_id : form_id,
			event   : event, 
		};

		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( true == response.data.status ) {
					jQuery( '.mwb-' + crm + '-gf__feed-list' ).slideUp();
					jQuery( '.mwb-' + crm + '-gf__feed-list' ).html( response.data.feeds );
					jQuery( '.mwb-' + crm + '-gf__feed-list' ).slideDown();
				}
			} else {
				triggerError( response.msg );
			}
		} );
	} );
	
	/* Toggle feeds fields */
	jQuery( '.mwb_' + crm + '_gf__feeds-wrap .mwb-feeds__meta-box-main-wrapper' ).slideDown();
	jQuery( '.mwb_' + crm + '_gf__feeds-wrap .mwb-feeds__header-link' ).addClass( 'link-active' );
	jQuery( document ).on( 'click', 'a.mwb-feeds__header-link', function() {
		jQuery( this ).toggleClass( 'link-active' );
		jQuery( this ).siblings( '.mwb-feeds__meta-box-main-wrapper' ).slideToggle( 500 );
	} );

	/**==================================================
						Logs Section JS.
    ====================================================*/

	/* Clear Log */
	jQuery( '#mwb-' + crm + '-gf-clear-log' ).on( 'click', function( e ) {
		e.preventDefault();

		var event  = 'clear_sync_log';
		var button = jQuery( this );
		button.text( 'Loading...' );

		let input = {
			action : action,
			nonce  : nonce,
			event  : event,
		};

		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				if ( true == response.data.success ) {
					button.text( 'Clear Log' );
					window.location.reload();
				}
			} else {
				triggerError( error );
			}
		} );
	} );

	/* Download log */
	jQuery( '#mwb-' + crm + '-gf-download-log' ).on( 'click', function( e ) {
		e.preventDefault();

		var event  = 'download_sync_log';
		var button = jQuery( this );
		button.text( 'Loading...' );

		let input = {
			action : action,
			nonce  : nonce,
			event  : event,
		};

		let result = doAjax( input );
		result.then( ( response ) => {
			if ( true == response.status ) {
				button.text( 'Download' );
				if ( true == response.data.success ) {
					location.href = response.data.redirect;
				} else if ( false == response.data.success ) {
					triggerError( response.data.msg );
				}
			} else {
				triggerError( error );
			}
		} );	
	} );

	
} );