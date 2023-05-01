jQuery(document).ready(($) => {
 
    // validar si se hace click
    $("#btnnuevo").click(()=>{
        $("#newModal").modal("show");
        console.log('Hiciste click')
    })
    
    let i = 1;
    $("#add").click(()=>{

        i++;
        $("#camposdinamicos").append('<tr class="row' + i + '">'+
                    '<td>'+
                        '<label for="name" class="form-label">Pregunta ' + i+ '</label>'+
                   ' </td>'+
                   '<td>'+
                        '<input type="text" name="name[]" id="name" class="form-control">'+
                   '</td>'+
                   '<td>'+
                        '<select name="type[]" id="type" class="form-control type_list">'+
                            '<option value="1" selected>SI - NO</option>'+
                            '<option value="2">Rango 0 - 5</option>'+
                        '</select>'+
                    '</td>'  +
                   ' <td>'+
                        '<button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button>'+
                    '</td>'+ 
                                     
                '</tr>');
       
    });

    $(document).on('click', '.btn_remove', (event) => {
        console.log('revmoved');
        let buttonId = event.target.id;
        $('.row' + buttonId).remove();
    });
    
    $(document).on('click', '[data-id]', (event) => {
        let id = event.target.getAttribute('data-id');
        let url = SolicitudesAjax.url;
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                action:'deleteRequest',
                nonce: SolicitudesAjax.security,
                id:id
            },
            success:()=>{
                alert('Datos borrados');
                location.reload();
            },
        })
    });

  });