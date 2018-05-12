// Delete prompt
$('body').on('click', '.deleteButton', function() {
  var reply = confirm('Are you sure you want to delete?');
  return reply;
});

// Add menu item form rows
$('.menu-section').on('click', '.add-item-row', function() {
    console.log('clicked')
    $row = $(this).prev('.menu-item').clone();
    console.log($row)
    $row.find('input[name="items[menu_item_id][]"]').val('');
    $row.find('select').val('default');
    $row.find('input[name="items[description][]"]').val('');
    $row.find('input[name="items[price][]"]').val('');
    $(this).before($row);
});
