function confirmarSweet(icon, pregunta, ruta)
{
    Swal.fire({
        title: pregunta,
        icon: icon,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Si',
        confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'NO' 
      }).then((result) => {
        
        if (result.isConfirmed) {
          window.location=ruta;
        }  
      })
}
function buscador()
 {
    if(document.getElementById('s').value==0)
    {
        return false;
    }
    document.search.submit();
 }
 function validaCorreo(valor) {
  if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(valor)){
   return true;
  } else {
   return false;
  }
}
 function tamila_tienda_login( )
 {
    var form=document.tamila_tienda_login_form;
    
    
    if(form.correo.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo E-Mail es obligatorio',
    });
    form.correo.value='';
    return false;
    }
    if(validaCorreo(form.correo.value)==false){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'El E-Mail ingresado no es válido',
        });
        form.correo.value=''; 
        return false;
    }
    if(form.password.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Contraseña es obligatorio',
    });
    form.password.value='';
    return false;
    }
    
    form.submit();
 }
 function tamila_tienda_registro( ){
  let form=document.tamila_tienda_registro_form;
  if(form.nombre.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo Nombre es obligatorio',
  });
  form.nombre.value='';
  return false;
  }
  if(form.apellido.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo Apellido es obligatorio',
  });
  form.apellido.value='';
  return false;
  }
  if(form.correo.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo E-Mail es obligatorio',
  });
  form.correo.value='';
  return false;
  }
  if(validaCorreo(form.correo.value)==false){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'El E-Mail ingresado no es válido',
      });
      form.correo.value=''; 
      return false;
  }
  if(form.password.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo Contraseña es obligatorio',
  });
  form.password.value='';
  return false;
  }
  if(form.password.value!=form.password2.value)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'Las Contraseñas no coinciden',
  });
  form.password.value='';
  form.password2.value='';
  return false;
  }
  form.submit();
 }
 function tamila_tienda_perfil( ){
  let form=document.tamila_tienda_registro_form;
  if(form.nombre.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo Nombre es obligatorio',
  });
  form.nombre.value='';
  return false;
  }
  if(form.apellido.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo Apellido es obligatorio',
  });
  form.apellido.value='';
  return false;
  }
  if(form.correo.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo E-Mail es obligatorio',
  });
  form.correo.value='';
  return false;
  }
  if(validaCorreo(form.correo.value)==false){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'El E-Mail ingresado no es válido',
      });
      form.correo.value=''; 
      return false;
  }
  if(form.password.value==0)
  { 
  
  }else{
    if(form.password.value!=form.password2.value)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Las Contraseñas no coinciden',
    });
    form.password.value='';
    form.password2.value='';
    return false;
    }
  }
 
  form.submit();
 }
 function cerrarSesion(ruta)
 {
     Swal.fire({
         title: 'Realmente deseas cerrar tu sesión?',
         icon: 'info',
         showDenyButton: true,
         showCancelButton: true,
         confirmButtonText: 'Si',
         confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             cancelButtonText: 'NO' 
       }).then((result) => {
         
         if (result.isConfirmed) {
           window.location=ruta;
         }  
       })
 }
function tamila_tienda_checkout_submit(){
let form=document.tamila_tienda_checkout;
    if(form.telefono.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Teléfono es obligatorio',
    });
    form.telefono.value='';
    return false;
    }
    if(form.direccion.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Dirección es obligatorio',
    });
    form.direccion.value='';
    return false;
    }
    if(form.ciudad.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Ciudad es obligatorio',
    });
    form.ciudad.value='';
    return false;
    }
    form.submit();
}
function tamila_tienda_restablecer( ){
    let form=document.tamila_tienda_restablecer_form;
    
    if(form.correo.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo E-Mail es obligatorio',
    });
    form.correo.value='';
    return false;
    }
    if(validaCorreo(form.correo.value)==false){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'El E-Mail ingresado no es válido',
        });
        form.correo.value=''; 
        return false;
    }
    
    form.submit();
   }
   function tamila_tienda_reset(){
    let form=document.tamila_tienda_reset_form;
    if(form.password.value==0)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'El campo Contraseña es obligatorio',
  });
  form.password.value='';
  return false;
  }
  if(form.password.value!=form.password2.value)
  { 
  Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'Las Contraseñas no coinciden',
  });
  form.password.value='';
  form.password2.value='';
  return false;
  }
  form.submit();
   }
function limpiar_carrito(){
    
    Swal.fire({
        title: 'Realmente deseas quitar los productos del carrito?',
        icon: 'info',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Si',
        confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'NO' 
      }).then((result) => {
        
        if (result.isConfirmed) {
          document.tamila_tienda_form_limpiar_carrito.submit();
        }  
      })
}
function tamila_tienda_comprar(id , url, nonce, retorno, accion){
    jQuery(document).ready(function($){
      
          $.ajax({
            type: "POST",
            url: url,
            data:{
                action : "tamila_tienda_comprar_ajax",
                nonce : nonce,
                id: id,
                accion:accion,
                carro_detalle_id:document.tamila_tienda_form_single.carro_detalle_id.value,
                product_quanity:document.tamila_tienda_form_single.product_quanity.value,
            },
            success:function(resp){ 
                window.location=retorno;
                //alert(resp);
                return false;
            }
        });
    });
   }