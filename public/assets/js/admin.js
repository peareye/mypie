// Delete prompt
$('body').on('click', '.deleteButton', function() {
  var reply = confirm('Are you sure you want to delete?');
  return reply;
});

// Auto grow user list
$('#user-emails').on('click', '.addEmail', function () {
    var newRow = $(this).clone();
    $(this).removeClass('addEmail');
    $(this).after(newRow);
});

// Add menu item form rows
$('.menu-section').on('click', '.add-item-row', function() {
    var $row = $(this).prev('.menu-item').clone();
    $row.find('input[name="items[menu_item_id][]"]').val('');
    $row.find('select').val('default');
    $row.find('input[name="items[description][]"]').val('');
    $row.find('input[name="items[price][]"]').val('');
    $row.find('.optional-button').remove();
    $(this).before($row);
});

// Delete menu item
$('.menu-section').on('click', '.delete-menu-item', function(e) {
    e.preventDefault();
    var menuItemId = $(this).parents('.menu-item').find('input[name="items[menu_item_id][]"]').val();
    var $menuItemRow = $(this).parents('.menu-item');

    if (confirm('Are you sure you want to delete?')) {
        // If no ID has been set, just remove row
        if (!Number.isInteger(parseInt(menuItemId))) {
            $menuItemRow.fadeOut(function() {
                $(this).slideUp().remove();
            });
            return;
        }
        // Otherwise hard delete
        $.ajax({
            url: '/admin/deletemenuitem/' + menuItemId,
            method: 'GET',
            success: function(r) {
                if (r.status === 'success') {
                    $menuItemRow.fadeOut(function() {
                        $(this).slideUp().remove();
                    });
                } else {
                    alert('There was an error. Please contact Moritz Media.')
                }
            },
            error: function(r) {
                alert('Error, something unexpected happened.');
            }
        });
    }
});

//
// Manage Item Defaults
//
// Add menu item form rows
$('.menu-item-defaults').on('click', '.add-item-default-row', function() {
    var $row = $(this).prev('.item-default').clone();
    $row.find('input[name="defaults[menu_item_default_id][]"]').val('');
    $row.find('input[name="defaults[kind][]"]').val('');
    $row.find('input[name="defaults[price][]"]').val('');
    $(this).before($row);
});

// Delete menu item default
$('.menu-item-defaults').on('click', '.delete-menu-item-default', function(e) {
    e.preventDefault();
    var menuItemId = $(this).parents('.item-default').find('input[name="defaults[menu_item_default_id][]"]').val();
    var $menuItemRow = $(this).parents('.item-default');

    if (confirm('Are you sure you want to delete?')) {
        // If no ID has been set, just remove row
        if (!Number.isInteger(parseInt(menuItemId))) {
            $menuItemRow.fadeOut(function() {
                $(this).slideUp().remove();
            });
            return;
        }
        // Otherwise hard delete
        $.ajax({
            url: '/admin/deletemenuitemdefault/' + menuItemId,
            method: 'GET',
            success: function(r) {
                if (r.status === 'success') {
                    $menuItemRow.fadeOut(function() {
                        $(this).slideUp().remove();
                    });
                } else {
                    alert('There was an error. Please contact Moritz Media.')
                }
            },
            error: function(r) {
                alert('Error, something unexpected happened.');
            }
        });
    }
});

// Set the default price on change of kind
$('.edit-menu').on('change', 'select[name="items[type][]"]', function() {
    var newKind = $(this).val();
    var price = priceList[newKind];
    $(this).parents('.menu-item').find('input[name="items[price][]"]').val(price);
});

// Make sure element name is one word
$('form input.jsRefNameValidate').on('blur', function() {
    var elementName = $(this).val();
    elementName = elementName.replace(/[^a-zA-Z0-9_]/g, '_');
    elementName = elementName.toLowerCase();
    $(this).val(elementName);
});

// Date picker
$( ".datepicker" ).datepicker({
    dateFormat: 'dd-mm-yy'
});

// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
      var forms = document.getElementsByClassName('validate-form');
      var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);
  })();
