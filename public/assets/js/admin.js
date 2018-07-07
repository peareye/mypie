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

var newMenuItemIndex = 1;

// Add menu item form rows
$('.menu-section').on('click', '.add-item-row', function(e) {
    e.preventDefault();
    var $row = $(this).prev('.menu-item').clone();
    $row.find('input[name$="[menu_item_id]"]').val('');
    $row.find('select').val('default');
    $row.find('input[name$="[description]"]').val('');
    $row.find('input[name$="[price]"]').val('');
    $row.find('input:checked').prop('checked', false);

    var $fields = $row.find('input, select');
    for (var i = $fields.length - 1; i >= 0; i--) {
        var oldName = $($fields[i]).attr('name');
        var newName = oldName.replace(/x?a?b?c?d?e?[0-9]+/, 'x'+newMenuItemIndex);
        $($fields[i]).attr('name', newName);
    }
    newMenuItemIndex++;
    $(this).before($row);
    $row.find("select[name$='[type]']").focus();
});

// Add menu item default form rows
$('.menu-item-defaults').on('click', '.add-item-default-row', function(e) {
    e.preventDefault();
    var $row = $(this).prev('.item-default').clone();
    $row.find('input:not([type="checkbox"])').val('');
    $row.find('input:checked').prop('checked', false);
    var $fields = $row.find('input');
    for (var i = $fields.length - 1; i >= 0; i--) {
        var oldName = $($fields[i]).attr('name');
        var newName = oldName.replace(/x?[0-9]+/, 'x'+newMenuItemIndex);
        $($fields[i]).attr('name', newName);
    }
    newMenuItemIndex++;
    $(this).before($row);
    $row.find("input[name$='[kind]']").focus();
});

// Set the menu item price on change of type
$('.edit-menu').on('change', "select[name$='[type]']", function() {
    var newKind = $(this).val();
    var price = priceList[newKind];
    $(this).parents('.menu-item').find("input[name$='[price]']").val(price);
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
