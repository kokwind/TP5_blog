function login() {  
    
    var username = document.getElementById("username");  
    var pass = document.getElementById("password");  

    if (username.value == "") {  
        alert("请输入用户名");  
        return false;
    } 
    else if (pass.value  == "") {  
        alert("请输入密码");  
        return false;
    }
    else{
        return true;
    }
}  