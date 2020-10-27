<html>
    <head>
        <title>
            User
        </title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <style>
            .error{
                color: red;
            }
            .input_div{
                display: none;
            }
            #error_div{
                display: none;
            }
        </style>
    </head>
    <body>
       <div class="container-fluid">
           <h1 style="text-align: center; margin-top: 15px; font-size: 30pt;">
               User Table
           </h1>
           <div id="error_div" class="alert alert-danger">
               <ul></ul>
           </div>
           <div style="margin: 10px; float: right;">
               <div style="display: inline;">
                   <label><input type="checkbox" id="select_all"> Select All</label>
               </div>
               <button class="btn btn-primary" data-toggle="modal" data-target="#add_new_modal">
                   Add New
               </button>
               <button class="btn btn-danger" id="bulk_delete_btn">
                   Bulk Delete
               </button>
           </div><br/>
           <div>

               <table class="table">

                   <thead>
                        <tr>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Hobby</th>
                            <th>Category</th>
                            <th>Profile Pic</th>
                            <th>Action</th>
                        </tr>
                   </thead>
                   <tbody>

                   </tbody>
               </table>
           </div>
       </div>
        <div class="modal fade" id="add_new_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Add New
                        </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['id'=>'add_new_form']) !!}
                            <div class="form-group">
                                {{Form::label('name', 'Name')}}
                                {{Form::text('name', '', ['class'=>'form-control'])}}
                                <span class="error" id="name_error"></span>
                            </div>
                        <div class="form-group">
                            {{Form::label('email', 'Email')}}
                            {{Form::text('email', '', ['class'=>'form-control'])}}
                            <span class="error" id="email_error"></span>
                        </div>
                        <div class="form-group">
                            {{Form::label('mobile_number', 'Mobile Number')}}
                            {{Form::text('mobile_number', '', ['class'=>'form-control', 'maxlength'=>'10'])}}
                            <span class="error" id="mobile_number_error"></span>
                        </div>
                        <div class="form-group">
                            {{Form::label('hobby', 'Hobby')}}<br/>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    {{Form::checkbox('hobby[]', 'Programming', false, ['class'=>'form-check-input'])}}
                                    Programming
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    {{Form::checkbox('hobby[]', 'Games', false, ['class'=>'form-check-input'])}}
                                    Games
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    {{Form::checkbox('hobby[]', 'Reading', false, ['class'=>'form-check-input'])}}
                                    Reading
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    {{Form::checkbox('hobby[]', 'Photography', false, ['class'=>'form-check-input'])}}
                                    Photography
                                </label>
                            </div><br/>
                            <span class="error" id="hobby_error"></span>
                        </div>
                        <div class="form-group">
                            {{Form::label('category_id', 'Category')}}
                            {{Form::select('category_id', $categories, '',['class'=>'form-control'])}}
                            <span class="error" id="category_error"></span>
                        </div>
                        <div class="form-group">
                            {{Form::label('profile_pic', 'Profile Pic')}}
                            {{Form::file('profile_pic', ['class'=>'form-control'])}}
                            <span class="error" id="profile_pic_error"></span>
                        </div>
                        <button class="btn btn-primary" id="save">Save</button>
                        {!! Form::close() !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function (){
            get_user();
            $("#add_new_form").submit(function (e){
                e.preventDefault();
                $("#save").prop('disabled', true).text('Please wait...');
                var formData = new FormData(this);
                $.ajax({
                    uri:"user",
                    type:"POST",
                    data: formData,
                    enctype: "multipart/form-data",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    success: function(resp){
                        $('#add_new_modal').modal('toggle');
                        $("#save").prop('disabled', false).text('Save');
                        $('form:input').attr('value', '')
                        console.log(resp);
                        swal({
                            title: "Success",
                            text: resp.message,
                            icon: 'success'
                        }).then((value)=>{
                            if(value){
                                get_user();
                            }
                        })
                    },
                    error: function(err){
                        console.log(err);
                        $("#save").prop('disabled', false).text('Save');
                        if(err.status == 422){
                            var errors = err.responseJSON.errors;
                            $.each(errors, function(key, value){
                                $("#"+key+"_error").text(value[0]);
                            })
                        }else{
                            swal({
                                title: 'Error',
                                text: err.responseJSON.message,
                                icon: 'error',
                            });
                        }
                    }
                })
            })

            $("#bulk_delete_btn").click(function(){
                multiple_delete();
            })
            $("#select_all").change(function (e){

                if(this.checked){
                    $('input[name="user_delete[]"]').prop('checked', true);
                }else{
                    $('input[name="user_delete[]"]').prop('checked', false);
                }
            })

            function get_user(){
                $('tbody').empty();
                $.ajax({
                    url:'user',
                    method:'GET',
                    dataType: "JSON",
                    success: function(resp){
                        console.log(resp)
                        var text = "";
                        var hobby_array = ['Programming', 'Games', 'Reading', 'Photography'];
                        $.each(resp.user, function(key, value){
                                var user_hobby_array = value.user_hobby.split(', ');
                                var hobby_check_box_text = "";
                                var category_list_text = "";
                                $.each(resp.categories, function(key, val){
                                   if(key== value.category_id){
                                       category_list_text += "<option value='"+key+"' selected>"+val+"</option>";
                                   }else{
                                       category_list_text += "<option value='"+key+"'>"+val+"</option>";
                                   }
                                });
                                $.each(hobby_array, function(key, value){
                                    if(user_hobby_array.indexOf(value) == -1){
                                        hobby_check_box_text += "<label><input type='checkbox' value='"+value+"' class='checkbox'>"+value+"</label>"
                                    }else{
                                        hobby_check_box_text += "<label><input type='checkbox' value='"+value+"' checked class='checkbox'>"+value+"</label>"
                                    }
                                });
                                text += "<tr><td><input type='checkbox' name='user_delete[]' value="+value.id+"></td>" +
                                    "<td class='col_name' data-col_name='name' id='name"+value.id+"'><div class='div"+value.id+"'>"+value.name+"</div><div class='input_div' id='input_div"+value.id+"name'><input type='text' value='"+value.name+"'></div></td>" +
                                    "<td class='col_name' data-col_name='email' id='email"+value.id+"'><div class='div"+value.id+"'>"+value.email+"</div><div class='input_div' id='input_div"+value.id+"email'><input type='text' value='"+value.email+"'></div></td>" +
                                    "<td class='col_name' data-col_name='mobile_number' id='mobile_number"+value.id+"'><div class='div"+value.id+"'>"+value.mobile_number+"</div><div class='input_div' id='input_div"+value.id+"mobile_number'><input type='text' value='"+value.mobile_number+"'></div></td>" +
                                    "<td><div class='div"+value.id+"'>"+value.user_hobby+"</div><div class='input_div' id='input_div"+value.id+"user_hobby'>"+hobby_check_box_text+"</div></td>" +
                                    "<td><div class='div"+value.id+"'>"+value.category.name+"</div><div class='input_div' id='input_div"+value.id+"category'><select>"+category_list_text+"</select></div></td>" +
                                    "<td><div class='div"+value.id+"'><img src='"+value.profile_pic+"' height='80' width='100' style='display: block;'></div><div class='input_div' id='input_div"+value.id+"profile_pic'><input type='file'></div> " +
                                    "</td><td><button class='btn btn-success' id='edit_"+value.id+"'>Edit</button>&nbsp; &nbsp;<button class='btn btn-danger' id='delete_"+value.id+"'>Delete</button></td>" +
                                    "</tr>";
                        })
                        $('tbody').append(text);

                    },
                    error: function(err){
                        console.log(err)
                    }
                })
            }

            $('tbody').on('click', '.btn-danger', function(e){
                var data = $(this).attr('id').split('_');
                console.log(data)
                single_delete(data[1]);
            })

            $('tbody').on('click', '.btn-success', function(e){
                var data = $(this).attr('id').split('_');
                if(data[0]=='edit'){
                    $('#input_div'+data[1]+'name,'+'#input_div'+data[1]+'email,'+'#input_div'+data[1]+'mobile_number,'+'#input_div'+data[1]+'user_hobby,'+'#input_div'+data[1]+'category,'+'#input_div'+data[1]+'profile_pic').show();
                    $('.div'+data[1]).hide();
                    $(this).attr('id', 'update_'+data[1]).text('Update')
                }else if(data[0]=='update'){
                   update(data[1]);
                }
            });

            function single_delete(id){
                swal({
                    title: "Are you sure ?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((value => {
                    if(value){
                        $.ajax({
                            url: 'user/'+id,
                            method: "DELETE",
                            dataType: "JSON",
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success:function(resp){
                                console.log(resp)
                                swal(resp.message, {
                                    icon: "success",
                                }).then(value=>{
                                    get_user();
                                });
                            },
                            error:function (err){
                                console.log(err);
                            }
                        })
                    }else{
                        swal("Your data is safe!");
                    }
                }))
            }

            function multiple_delete(){
                var  user_delete = $("input[name='user_delete[]']:checked");
                if(user_delete.length){
                    swal({
                        title: "Are you sure ?",
                        text: "Once deleted, you will not be able to recover this data!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((value => {
                        if(value){
                            user_array = new Array();
                            user_delete.each(function () {
                                user_array.push($(this).val());
                            });
                            console.log(user_array);
                            $.ajax({
                                url: 'user/'+user_array,
                                method: "DELETE",
                                dataType: "JSON",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                },
                                success:function(resp){
                                    console.log(resp)
                                    swal(resp.message, {
                                        icon: "success",
                                    }).then(value=>{
                                        get_user();
                                    });
                                },
                                error:function (err){
                                    console.log(err);

                                        swal({
                                            title: 'Error',
                                            text: 'Sorry! some going wrong',
                                            icon: 'error',
                                        });

                                }
                            })
                        }else{
                            swal("Your data is safe!");
                        }
                    }))

                }else{
                    alert("No row selected ");
                }

            }

            function update(id){

                    $("#update_"+id).prop('disabled', true).text('Please wait...');
                    hobby_data = [];
                    name = $('#input_div'+id+'name').find('input').val();
                    email = $('#input_div'+id+'email').find('input').val();
                    mobile_number = $('#input_div'+id+'mobile_number').find('input').val();
                    $('#input_div'+id+'user_hobby').find('input:checked').each(function(){
                        hobby_data.push($(this).val());
                    });
                    category = $('#input_div'+id+'category').find('select').val();
                    var data = new FormData();
                    data.append('name', name);
                    data.append('email', email);
                    data.append('mobile_number', mobile_number);
                    data.append('category_id', category);
                    data.append('hobby', hobby_data);
                    if($('#input_div'+id+'profile_pic').find('input')[0].files[0]){
                        data.append('profile_pic', $('#input_div'+id+'profile_pic').find('input')[0].files[0])
                    }
                    data.append('_token', '{{csrf_token()}}');
                    data.append('_method', 'PUT');
                    $.ajax({
                        url: 'user/'+id,
                        type: 'POST',
                        data: data,
                        enctype: "multipart/form-data",
                        dataType: "JSON",
                        processData: false,
                        contentType: false,
                        success: function (resp){
                            console.log(resp);
                            $('#update'+id).prop('disabled', false).text('Update');
                            swal(resp.message,{
                                icon: 'success',
                            }).then((()=>{
                                get_user();
                            }))
                        },
                        error: function (err){
                            $('#update'+id).prop('disabled', false).text('Update');
                            var error_div_html = $("#error_div").find('ul')
                            if(err.status == 422){
                                var errors = err.responseJSON.errors;
                                error_div_html.empty();
                                $.each(errors, function(key, value){
                                    var list_html = "<li>"+value[0]+"</li>"
                                    error_div_html.append(list_html)
                                })
                                $("#error_div").show();
                            }else{
                                swal({
                                    title: 'Error',
                                    text: err.responseJSON.message,
                                    icon: 'error',
                                })
                            }
                        }
                    })

            }
        })


    </script>
</html>
