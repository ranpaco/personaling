/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function changeFilter(e){
   var column = $(this);
   
   //si es fecha
   if(column.val() === 'lastvisit_at') //Fecha de carga
   {
       dateFilter(column);
    
   }else if(column.val() === 'status' || column.val() === 'tipoUsuario'
             || column.val() === 'fuenteR') //Estado del usuario, tipo usuario, fuenteRegistro
   {       
       
        listFilter(column, column.val());
        

   }else if(column.val() === 'first_name' || column.val() === 'last_name'
                || column.val() === 'email' || column.val() === 'telefono'
                || column.val() === 'ciudad') 
   {
       textFilter(column);       
        
   }else //campo normal (numérico)
   {      
      valueFilter(column);       
      
   }
    
}