function validaCorreo(valor) {
  if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(valor)){
   return true;
  } else {
   return false;
  }
}
 
 function confirmaAlert(pregunta, ruta) {
     jCustomConfirm(pregunta, 'Tamila', 'Aceptar', 'Cancelar', function(r) {
         if (r) {
             window.location = ruta;
         }
     });
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
 function confirmarSweet(pregunta, ruta)
 {
     Swal.fire({
         title: pregunta,
         icon: 'error',
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
 function soloNumeros(evt) {
     key = (document.all) ? evt.keyCode : evt.which;
     //alert(key);
     if (key == 17) return false;
     /* digitos,del, sup,tab,arrows*/
     return ((key >= 48 && key <= 57) || key == 8 || key == 127 || key == 9 || key == 0);
 }
 function get_respuestas_formulario(id){
  
    jQuery(document).ready(function($){
        $("#respuesta_modal").modal("show");
        document.getElementById('respuesta_modal_title').innerHTML="Respuestas formulario N°"+id;
        $.ajax({
            type: "POST",
            url: datosajax.url,
            data:{
                action : "tamila_curso_1_contac_respuestas_ajax",
                nonce : datosajax.nonce,
                id: id,
            },
            success:function(resp){ 
                $("#respuesta_modal_body").html(resp);
                return false;
            }
        });
    });
    
 }
 function get_crear_formulario(que, title, nombre, correo, id){
    jQuery(document).ready(function($){
        $("#crear_formulario").modal("show"); 
        document.getElementById('crear_formulario_title').innerHTML=title;
        document.getElementById('tamila_curso_1_contact_nombre').value=nombre;
        document.getElementById('tamila_curso_1_contact_correo').value=correo;
        if(que=='1'){
            document.getElementById('tamila_curso_1_contact_accion').value='1';
        }else{
            document.getElementById('tamila_curso_1_contact_accion').value='2';
            document.getElementById('tamila_curso_1_contact_id').value=id;
            
        }
    });
 }
 function tamila_curso_1_contact(){
    var form=document.tamila_curso_1_contact_form_crear;
    if(form.nombre.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo nombre es obligatorio',
    });
    form.nombre.value='';
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
    
    form.submit();
 }
 function get_eliminar_formulario(id){
    Swal.fire({
        title: 'Realmente desea eliminar este registro?',
        icon: 'warning',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Si',
        confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'NO' 
      }).then((result) => {
        
        if (result.isConfirmed) {
             
          document.tamila_curso_1_contac_form_eliminar.accion.value='3';
          document.tamila_curso_1_contac_form_eliminar.id.value=id;
          document.tamila_curso_1_contac_form_eliminar.submit();
        }  
      })
 }
  
 
 
  
 
 

 