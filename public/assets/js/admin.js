// Delete prompt
$('body').on('click', '.deleteButton', function() {
  var reply = confirm('Are you sure you want to delete?');
  return reply;
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

// Delete menu item asynchronosly
$('.menu-section').on('click', '.delete-menu-item', function(e) {
    e.preventDefault();
    var menuItemId = $(this).parents('.menu-item').find('input[name="items[menu_item_id][]"]').val();
    var $menuItemRow = $(this).parents('.menu-item');

    if (confirm('Are you sure you want to delete?')) {
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