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
        document.getElementById('respuesta_moda_title').innerHTML="Respuestas formulario N°"+id;
        $.ajax({
            type: "POST",
            url: datosajax.url,
            data:{
                action : "tamila_form_contact_respuestas_ajax",
                nonce : datosajax.nonce,
                id: id,
            },
            success:function(resp){
                //document.getElementById('respuesta_moda_body').innerHTML=resp;
                $("#respuesta_moda_body").html(resp);
                return false;
            }
        });
    });
    
 }
 function get_crear_formulario(que, title, nombre, correo, id){
    jQuery(document).ready(function($){
        $("#tamila_galeria_crear").modal("show"); 
        document.getElementById('tamila_galeria_crear_title').innerHTML=title;
        document.getElementById('tamila_galeria_nombre').value=nombre;
        if(que=='1'){
            document.getElementById('tamila_galeria_que').value='1';
        }else{
            document.getElementById('tamila_galeria_que').value='2';
            document.getElementById('tamila_galeria_id').value=id;
            
        }
    });
 }
 function tamila_galeria_crear(){
    var form=document.tamila_galeria_crear_form;
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
    
    
    
    form.submit();
 }
 function get_eliminar_galeria(id){
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
             
          document.tamila_form_contact_form_eliminar.accion.value='3';
          document.tamila_form_contact_form_eliminar.id.value=id;
          document.tamila_form_contact_form_eliminar.submit();
        }  
      });
      return false;
 }
 function get_eliminar_foto_slide(id){
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
          var form=document.tamila_productos_galeria_agregar_foto;
          form.tamila_productos_galeria_agregar_foto_foto_id.value=id; 
          form.accion.value='3';
          form.submit();
        }  
      });
      return false;
 }
 
 //media de wordpress
 jQuery(document).ready(function($){
  var marco, $btn_marco=$('.btnMarco');
  $btn_marco.on('click', function(){
    if (marco){
      marco.open();
      return;
    }
    var marco = wp.media({
      frame: 'select',
      title: 'Seleccionar imagen para la galería',
      button: {
          text: 'Usar esta imagen'
      },
      multiple: false,
      library: {
          type: 'image',
          order:'DESC',
          orderby:'name'
      }
  });
  marco.on( 'select', function(){
            
    //console.log(  marco.state().get('selection').first().toJSON()  );return false;
   // console.log(  marco.state().get('selection').first().toJSON().id  );
   // console.log(marco.state().get('selection').first().toJSON().filename);
    //console.log(marco.state().get('selection').first().toJSON().url);
         
    let form=document.tamila_productos_galeria_agregar_foto;
          
          form.tamila_productos_galeria_agregar_foto_foto_id.value=  marco.state().get('selection').first().toJSON().id;
           
          form.tamila_productos_galeria_agregar_foto_url.value=  marco.state().get('selection').first().toJSON().url;
          form.submit();
          
        });
        
        marco.open();
  });
});
 
 
function get_eliminar_foto_galeria(id){
    Swal.fire({
            title: 'Realmente desea eliminar este registro?',
            icon: 'error',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Si',
            confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'NO' 
          }).then((result) => {
            
            if (result.isConfirmed) {
               document.tamila_productos_galeria_eliminar_foto.tamila_productos_galeria_eliminar_foto_id.value=id;
              document.tamila_productos_galeria_eliminar_foto.submit();
            }  
          })}
function get_pasarelas(id, nombre){
  jQuery(document).ready(function($){
    $("#tamila_tienda_pasarelas_modal").modal("show");
        document.getElementById('tamila_tienda_pasarelas_modal_title').innerHTML="Datos pasarela: <strong>"+nombre+"</strong>";
        $.ajax({
          type: "POST",
          url: datosajax.url,
          data:{
              action : "tamila_tienda_pasarelas_ajax",
              nonce : datosajax.nonce,
              id: id,
          },
          success:function(resp){ 
              $("#tamila_tienda_pasarelas_modal_body").html(resp);
              return false;
          }
      });
  });
 }
 function get_variables_globales(id, nombre){
  jQuery(document).ready(function($){
    $("#tamila_tienda_variables_globales_modal").modal("show");
        document.getElementById('tamila_tienda_variables_globales_modal_title').innerHTML="Datos pasarela: <strong>"+nombre+"</strong>";
        $.ajax({
          type: "POST",
          url: datosajax.url,
          data:{
              action : "tamila_tienda_variables_globales_ajax",
              nonce : datosajax.nonce,
              id: id,
          },
          success:function(resp){ 
              $("#tamila_tienda_variables_globales_modal_body").html(resp);
              return false;
          }
      });
  });
 }
 function get_detalle_venta(id, nombre){
    jQuery(document).ready(function($){
      $("#tamila_tienda_ventas_modal").modal("show");
          document.getElementById('tamila_tienda_ventas_modal_title').innerHTML="Datos venta N°: <strong>"+nombre+"</strong>";
          $.ajax({
            type: "POST",
            url: datosajax.url,
            data:{
                action : "tamila_tienda_ventas_ajax",
                nonce : datosajax.nonce,
                id: id,
            },
            success:function(resp){ 
                $("#tamila_tienda_ventas_modal_body").html(resp);
                return false;
            }
        });
    });
   }
   function get_editar_venta(id, nombre){
    jQuery(document).ready(function($){
      $("#tamila_tienda_ventas_modal").modal("show");
          document.getElementById('tamila_tienda_ventas_modal_title').innerHTML="Editar venta N°: <strong>"+nombre+"</strong>";
          $.ajax({
            type: "POST",
            url: datosajax.url,
            data:{
                action : "tamila_tienda_ventas_editar_ajax",
                nonce : datosajax.nonce,
                id: id,
            },
            success:function(resp){ 
                $("#tamila_tienda_ventas_modal_body").html(resp);
                return false;
            }
        });
    });
   }
   function get_filtro_venta(id, nombre){
    jQuery(document).ready(function($){
      $("#tamila_tienda_ventas_modal").modal("show");
          document.getElementById('tamila_tienda_ventas_modal_title').innerHTML="Filtro por: <strong>"+nombre+"</strong>";
          $.ajax({
            type: "POST",
            url: datosajax.url,
            data:{
                action : "tamila_tienda_ventas_filtro_ajax",
                nonce : datosajax.nonce,
                id: id,
            },
            success:function(resp){ 
                $("#tamila_tienda_ventas_modal_body").html(resp);
                return false;
            }
        });
    });
   }
 function edit_pasarela()
 {
    let form=document.tamila_tienda_form_pasarela;
    if(form.url.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo URL es obligatorio',
    });
    form.url.value='';
    return false;
    }
    if(form.cliente_id.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Cliente ID es obligatorio',
    });
    form.cliente_id.value='';
    return false;
    }
    if(form.cliente_secret.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Cliente Secret es obligatorio',
    });
    form.cliente_secret.value='';
    return false;
    }
    form.return.value=location.href;
    form.submit();
 }
 function edit_variables_globales()
 {
    let form=document.tamila_tienda_form_variables_globales;
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
    if(form.valor.value==0)
    { 
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'El campo Valor es obligatorio',
    });
    form.valor.value='';
    return false;
    }
     
    form.return.value=location.href;
    form.submit();
 }

 function send_editar_ventas(){
    let form=document.tamila_tienda_form_ventas;
    form.return.value=location.href;
    form.submit();
 }
 function send_form_filtro_venta( url){
    let form=document.form_filtro_venta;
     
    window.location=url+"&filtro=1&filtro_tipo="+form.filtro_tipo.value+"&filtro_valor="+form.filtro_valor.value;
 }