	$('#recepcion_inicio_day').on('change', validar_recepcion_inicio);
	$('#recepcion_inicio_month').on('change', validar_recepcion_inicio);
	$('#recepcion_inicio_year').on('change', validar_recepcion_inicio);
	
	$('#recepcion_fin_day').on('change', validar_recepcion_fin);
	$('#recepcion_fin_month').on('change', validar_recepcion_fin);
	$('#recepcion_fin_year').on('change', validar_recepcion_fin);
	
	$('#ventas_inicio_day').on('change', validar_ventas_inicio);
	$('#ventas_inicio_month').on('change', validar_ventas_inicio);
	$('#ventas_inicio_year').on('change', validar_ventas_inicio);
	
	$('#ventas_fin_day').on('change', validar_ventas_fin);
	$('#ventas_fin_month').on('change', validar_ventas_fin);
	$('#ventas_fin_year').on('change', validar_ventas_fin);
	
	function validar_recepcion_inicio(){
		var day = $('#recepcion_inicio_day').val();
		var month = $('#recepcion_inicio_month').val();
		var year = $('#recepcion_inicio_year').val();
		
		console.log('validar');
		if(day != '-1' && month != '-1' && year != '-1'){
			if(validar_fecha(day, month, year)){
				$('#Campana_recepcion_inicio').val(year+'-'+month+'-'+day+' 00:00:01');
			}else{
				$('#recepcion_inicio_day').val('-1');
				$('#recepcion_inicio_month').val('-1');
				$('#recepcion_inicio_year').val('-1');
			}
		}
	}
	
	function validar_recepcion_fin(){
		var day = $('#recepcion_fin_day').val();
		var month = $('#recepcion_fin_month').val();
		var year = $('#recepcion_fin_year').val();
		
		//console.log('validar');
		if(day != '-1' && month != '-1' && year != '-1'){
			console.log('pasa');
			if(validar_fecha(day, month, year)){
				$('#Campana_recepcion_fin').val(year+'-'+month+'-'+day+' 00:00:01');
			}else{
				$('#recepcion_fin_day').val('-1');
				$('#recepcion_fin_month').val('-1');
				$('#recepcion_fin_year').val('-1');
			}
		}
	}
	
	function validar_ventas_inicio(){
		var day = $('#ventas_inicio_day').val();
		var month = $('#ventas_inicio_month').val();
		var year = $('#ventas_inicio_year').val();
		
		//console.log('validar');
		if(day != '-1' && month != '-1' && year != '-1'){
			if(validar_fecha(day, month, year)){
				$('#Campana_ventas_inicio').val(year+'-'+month+'-'+day+' 00:00:01');
			}else{
				$('#ventas_inicio_day').val('-1');
				$('#ventas_inicio_month').val('-1');
				$('#ventas_inicio_year').val('-1');
			}
		}
	}
	
	function validar_ventas_fin(){
		var day = $('#ventas_fin_day').val();
		var month = $('#ventas_fin_month').val();
		var year = $('#ventas_fin_year').val();
		
		//console.log('validar');
		if(day != '-1' && month != '-1' && year != '-1'){
			if(validar_fecha(day, month, year)){
				$('#Campana_ventas_fin').val(year+'-'+month+'-'+day+' 00:00:01');
			}else{
				$('#ventas_fin_day').val('-1');
				$('#ventas_fin_month').val('-1');
				$('#ventas_fin_year').val('-1');
			}
		}
	}
	
	function validar_fecha(dia, mes, anio){
        var numDias = 31;
        
        //console.log('Dia: '+dia+' - Mes: '+mes+' - Año: '+anio);
        
        if(mes == 4 || mes == 6 || mes == 9 || mes == 11){
            numDias = 30;
        }
        
        if(mes == 2){
            if(comprobarSiBisisesto(anio)){
                numDias = 29;
            }else{
                numDias = 28;
            }
        }
        
        if(dia > numDias){
            return false;
        }
        return true;
    }
    
    function comprobarSiBisisesto(anio){
        if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
            return true;
        }
        else {
            return false;
        }
    }