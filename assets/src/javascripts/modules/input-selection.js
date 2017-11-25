export default (item) => {
    let val = item.val();

    if (val)
        item.parent().addClass('filled');
    else
        item.parent().removeClass('filled');
}