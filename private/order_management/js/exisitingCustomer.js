document.body.addEventListener('existingCustomer', function (evt) {
  const select = document.getElementById('customerId');
  const customer = select.querySelector(`option[value='${evt.detail.value}']`);
  customer.selected = true;
});
