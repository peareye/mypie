// Delete prompt
$('body').on('click', '.jsDeleteButton', function() {
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
$('.menu-section').on('click', '.add-item-row', function(e) {
    e.preventDefault();
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

// Add menu item default form rows
var newMenuItemDefaultIndex = 1;
$('.menu-item-defaults').on('click', '.add-item-default-row', function(e) {
    e.preventDefault();
    var $row = $(this).prev('.item-default').clone();
    $row.find('input:not([type="checkbox"])').val('');
    $row.find('input:checked').prop('checked', false);
    var $fields = $row.find('input');
    for (var i = $fields.length - 1; i >= 0; i--) {
        var oldName = $($fields[i]).attr('name');
        var newName = oldName.replace(/x?[0-9]+/, 'x'+newMenuItemDefaultIndex);
        $($fields[i]).attr('name', newName);
    }
    newMenuItemDefaultIndex++;
    $(this).before($row);
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
