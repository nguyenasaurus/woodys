/**
* @desc Lodgix
* @author Lodgix  - http://www.lodgix.com
*/


function p_lodgix_set_demo_credentials()
{
    jQueryLodgix('#p_lodgix_owner_id')[0].value = '13';
  	jQueryLodgix('#p_lodgix_api_key')[0].value = 'f89bd3b1bd098af107d727063c2736a6';
}

function toggle_lodgix_featured_property(id) {
    var checked = jQueryLodgix('#lodgix_featured_property_' + id).is(':checked');

    jQueryLodgix.ajax({
        type: "POST",
        url: p_lodgix_ajax.ajaxURL + '?action=p_lodgix_toggle_featured',
        data: {'id': id, 'checked': checked},
        dataType: 'json',
        success: function(data) {
            
        }
    });
}

function lodgix_settings_tab_activate(element, container, callback) {
    var $active    = container.find('> .active')
    var transition = callback
      && jQueryLodgix.support.transition
      && (($active.length && $active.hasClass('fade')) || !!container.find('> .fade').length)

    function next() {
      $active
        .removeClass('active')
        .find('> .dropdown-menu > .active')
          .removeClass('active')
        .end()
        .find('[data-toggle="tab"]')
          .attr('aria-expanded', false);

      element
        .addClass('active')
        .find('[data-toggle="tab"]')
          .attr('aria-expanded', true);

      if (transition) {
        element[0].offsetWidth; // reflow for transition
        element.addClass('in');
      } else {
        element.removeClass('fade');
      }

      if (element.parent('.dropdown-menu')) {
        element
          .closest('li.dropdown')
            .addClass('active')
          .end()
          .find('[data-toggle="tab"]')
            .attr('aria-expanded', true);
      }

      callback && callback();
    }

    $active.length && transition ?
      $active
        .one('bsTransitionEnd', next)
        .emulateTransitionEnd(Tab.TRANSITION_DURATION) :
      next();

    $active.removeClass('in');
  }

function lodgix_settings_tab_show(el) {
    var $this    = el;
    var $ul      = $this.closest('ul:not(.dropdown-menu)');
    var selector = $this.data('target');

    if (!selector) {
      selector = $this.attr('href');
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, ''); // strip for ie7
    }

    if ($this.parent('li').hasClass('active')) return;

    var $previous = $ul.find('.active:last a');
    var hideEvent = jQueryLodgix.Event('hide.bs.tab', {
      relatedTarget: $this[0]
    });
    var showEvent = jQueryLodgix.Event('show.bs.tab', {
      relatedTarget: $previous[0]
    });

    $this.trigger(showEvent);

    if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) return

    var $target = jQueryLodgix(selector);

    lodgix_settings_tab_activate($this.closest('li'), $ul);
    lodgix_settings_tab_activate($target, $target.parent(), function () {
      $previous.trigger({
        type: 'hidden.bs.tab',
        relatedTarget: $this[0]
      });
      $this.trigger({
        type: 'shown.bs.tab',
        relatedTarget: $previous[0]
      });
    });
  }

function set_thesis_2_theme_enabled() {
    if (document.getElementById('p_lodgix_thesis_2_compatibility').checked) {
        jQueryLodgix('#p_lodgix_thesis_2_template').removeAttr('disabled');
        jQueryLodgix('#p_lodgix_thesis_2_template').show();
    } else {
        jQueryLodgix('#p_lodgix_thesis_2_template').attr('disabled','disabled');
        jQueryLodgix('#p_lodgix_thesis_2_template').hide();
    }
}

function set_lodgix_page_template_enabled() {
    if (jQueryLodgix('#p_lodgix_page_template').val() == 'CUSTOM') {
        jQueryLodgix('#p_lodgix_custom_page_template').removeAttr('disabled');
        jQueryLodgix('#p_lodgix_custom_page_template').show();
    } else {
        jQueryLodgix('#p_lodgix_custom_page_template').attr('disabled','disabled');
        jQueryLodgix('#p_lodgix_custom_page_template').hide();
    }
}

function get_form_data($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};
    jQueryLodgix.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });
    return indexed_array;
}

function lodgix_submit_save(isReload) {
    jQueryLodgix('.lodgix_processing_message').html('');
    jQueryLodgix('.lodgix_processing_throbber').show();

    var data = get_form_data(jQueryLodgix('#p_lodgix_options'));

    data['p_lodgix_save'] = true;

    jQueryLodgix.ajax({
        type: "POST",
        url: p_lodgix_ajax.ajaxURL + '?action=p_lodgix_save_settings',
        data: data,
        dataType: 'json',
        success: function(data) {
            if ((isReload && data.result == 'OK') || data.result == 'RELOAD') {
                location.reload();
            } else {
                jQueryLodgix('#lodgix_properties_table').dataTable().api().ajax.reload()
                jQueryLodgix('#lodgix_properties_table').dataTable().fnDraw();
                jQueryLodgix('.lodgix_processing_throbber').hide();
                jQueryLodgix('.lodgix_processing_message').html(data.msg);
            }
        },
        error: function() {
            jQueryLodgix('.lodgix_processing_throbber').hide();
            jQueryLodgix('.lodgix_processing_message').html('Undefined Error: Please check your internet connection.');
        }
    });

    return false;
}

function lodgix_toggle_select_all() {
    var checked = jQueryLodgix('#lodgix_select_all').is(':checked');

    jQueryLodgix.ajax({
        type: "POST",
        url: p_lodgix_ajax.ajaxURL + '?action=p_lodgix_toggle_select_all',
        data: {'checked': checked},
        dataType: 'json',
        success: function(data) {
            jQueryLodgix('#lodgix_properties_table').dataTable().api().ajax.reload();
            jQueryLodgix('#lodgix_properties_table').dataTable().fnDraw();            
        }
    });
}

function lodgix_submit_clean() {
    jQueryLodgix('.lodgix_processing_message').html('');

    jQLodgix.confirm({
        title: 'Delete all property listings?',
        text: 'This will delete all data and settings. Are you sure you want to?',
        confirmButtonClass: 'btn-danger',
        confirm: function() {
            jQueryLodgix('.lodgix_processing_throbber').show();
            var data = get_form_data(jQueryLodgix('#p_lodgix_options'));

            data['p_lodgix_clean'] = true;

            jQueryLodgix.ajax({
                type: "POST",
                url: p_lodgix_ajax.ajaxURL + '?action=p_lodgix_clean_database',
                data: data,
                dataType: 'json',
                success: function() {
                    location.reload();
                },
                error: function() {
                    jQueryLodgix('.lodgix_processing_throbber').hide();
                    jQueryLodgix('.lodgix_processing_message').html('Undefined Error: Please check your internet connection.');
                }
            });
        }
    });

    return false;
}

jQueryLodgix(document).ready(function(){

    jQueryLodgix("#p_lodgix_options").validate({
        rules: {
            p_lodgix_owner_id: {
            required: true
          },
          p_lodgix_api_key: {
            required: true
          }
        },
        messages: {
          p_lodgix_owner_id: {
            // the p_lodgix_lang object is define using wp_localize_script() in function p_lodgix_script() 
            required: p_lodgix_lang.required,
            number: p_lodgix_lang.number,
            min: p_lodgix_lang.min
          },
          p_lodgix_api_key: {
            // the p_lodgix_lang object is define using wp_localize_script() in function p_lodgix_script() 
            required: p_lodgix_lang.required
          }
        }
    });

    var columns = [
        { "mDataProp": "order", "sClass": "centered", "bSortable": true },
        { "mDataProp": "id", "sClass": "centered", "bSortable": true },
        { "mDataProp": "name", "sClass": "lodgix_properties", "bSortable": true },
        { "mDataProp": "featured", "sClass": "centered", "bSortable": true }
    ];

    jQueryLodgix('#lodgix_properties_table').dataTable({
		'bProcessing': true,
		'bServerSide': false,
		'sAjaxSource': p_lodgix_ajax.ajaxURL + '?action=p_lodgix_properties_list&sEcho=1',
        "aoColumns": columns,
        "iDisplayLength": 50,
        "bAutoWidth": true
    });

    jQueryLodgix('#p_lodgix_thesis_compatibility').change(function(){
        jQueryLodgix('#p_lodgix_thesis_2_compatibility').prop('checked', false);
        set_thesis_2_theme_enabled(); 	  	
    });

    jQueryLodgix('#p_lodgix_thesis_2_compatibility').change(function(){
        jQueryLodgix('#p_lodgix_thesis_compatibility').prop('checked', false);
        set_thesis_2_theme_enabled();
    });

    jQLodgix('#ldgxSettingsTabs').tabCollapse({
        tabsClass: 'hidden-sm hidden-xs',
        accordionClass: 'visible-sm visible-xs'
    });

});
