let t=document.querySelector(".cate-group");t!==null&&t.addEventListener("click",function(c){let a=c.target,l=t.querySelector(".input-box");if([...a.classList].includes("btn-cate-add")){let e=document.createElement("input");e.classList.add("form-control","mt-2"),e.name="board_cate[]",e.placeholder="\uAC8C\uC2DC\uD310 \uCE74\uD14C\uACE0\uB9AC",l.appendChild(e)}});
