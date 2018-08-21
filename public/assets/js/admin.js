// Delete prompt
$('body').on('click', '.jsDeleteButton', function() {
  var reply = confirm('Are you sure you want to delete?');
  return reply;
});

var newFormRowIndex = 1;

// Add user row
$('#user-emails').on('click', '.jsAddUser', function (e) {
    e.preventDefault();
    var $row = $(this).prev('.user-row').clone();
    $row.find('input:not([type="checkbox"])').val('');
    $row.find('input:checked').prop('checked', false);
    $row.find('input[name$="[admin]"]').prop('disabled',false);
    $row.find('input[name$="[admin]"]').prop('checked',false);

    var $fields = $row.find('input');
    for (var i = $fields.length - 1; i >= 0; i--) {
        var oldName = $($fields[i]).attr('name');
        var newName = oldName.replace(/x?[0-9]+/, 'x'+newFormRowIndex);
        $($fields[i]).attr('name', newName);
    }
    newFormRowIndex++;
    $(this).before($row);
    $row.find("select[name$='[email]']").focus();
});

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
        var newName = oldName.replace(/x?[0-9]+/, 'x'+newFormRowIndex);
        $($fields[i]).attr('name', newName);
    }
    newFormRowIndex++;
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
        var newName = oldName.replace(/x?[0-9]+/, 'x'+newFormRowIndex);
        $($fields[i]).attr('name', newName);
    }
    newFormRowIndex++;
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

// Menu board
var menuBoard;
$('.jsOpenMenuBoard').on('click', function(e) {
    e.preventDefault();
    var url = $(this).prop('href');
    menuBoard = window.open(url, 'menuBoard')
});

var sellItemOutStates = {
    'sellIn': {'status': 'N', 'label': 'Sell In'},
    'sellOut': {'status': 'Y', 'label': 'Sell Out'}
}

$('.jsSellItemInOut').on('click', function(e) {
    e.preventDefault();
    var $link = $(this);
    var url = $link.prop('href');
    $.get({
        url: url,
        success: function(r) {
            if (r.menuItem.sold_out === 'N') {
                newUrl = url.replace(/status=[N,Y]/, 'status='+sellItemOutStates.sellOut.status)
                $link.prop('href', newUrl);
                $link.text(sellItemOutStates.sellOut.label)
            } else {
                newUrl = url.replace(/status=[N,Y]/, 'status='+sellItemOutStates.sellIn.status)
                $link.prop('href', newUrl);
                $link.text(sellItemOutStates.sellIn.label)
            }
            menuBoard.location.reload();
        }
    });
});

// Publish supplier
var supplierPublishedStates = {
    'Y': {'newFlag': 'N', 'buttonLabel': 'Unpublish', 'buttonClass': 'btn-danger'},
    'N': {'newFlag': 'Y', 'buttonLabel': 'Publish', 'buttonClass': 'btn-success'}
}

$('.jsPublishSupplier').on('click', function(e) {
    e.preventDefault();
    var $button = $(this);
    var flag = $button.data('flag');
    var id = $button.data('id');
    var publishUrl = $button.data('url');
    $.get({
        url: publishUrl + '/' + id + '/' + supplierPublishedStates[flag].newFlag,
        success: function(r) {
            $button.html(supplierPublishedStates[r.publishedStatus].buttonLabel)
                .data('flag', r.publishedStatus)
                .toggleClass(supplierPublishedStates[flag].buttonClass + ' ' + supplierPublishedStates[r.publishedStatus].buttonClass);
        }
    });
});
