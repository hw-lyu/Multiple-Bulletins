let cateGroup = document.querySelector('.cate-group');
if (cateGroup !== null) {
  cateGroup.addEventListener('click', function (e) {
    let eTarget = e.target,
      inputBox = cateGroup.querySelector('.input-box'),
      classListArr = [...eTarget.classList];

    if (classListArr.includes('btn-cate-add')) {
      let input = document.createElement('input');
      input.classList.add('form-control', 'mt-2');
      input.name = 'board_cate[]';
      input.placeholder = "게시판 카테고리";

      inputBox.appendChild(input);
    }
  });
}
