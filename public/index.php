<?php

use App\Controller\IndexController;

require_once '../vendor/autoload.php';

if(empty($_REQUEST['DOMAIN'])){exit;}

$index = new IndexController();
$data = $index->index($_REQUEST);

?>

<!doctype html>
<html lang="ua">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>Prom to Bitrix</title>
</head>
<body>

<header class='d-flex bg-info mb-2'>

    <div class="ms-3">
        <img src="./public/img/logo_prom.png" height="100">
    </div>

    <?php if($data['user']['id'] == 9): ?>
        <div class="w-100 row justify-content-center align-items-center text-center">
            <p class="pt-2 <?= $data['payed']['style'] ?>"><?= $data['payed']['title'] ?></p>
            <div class="d-flex justify-content-center align-items-center mb-1">
                <span><?= $data['payed']['text'] ?></span>
                <form class="mb-0" action="./public/payform.php" method="POST">
                    <input type="hidden" name="id" value="<?= $data['user']['id'] ?>">
                    <input type="hidden" name="domain" value="<?= $data['user']['domain'] ?>">
                    <button class="btn btn-success ms-3" type="submit">Подовжити...</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

</header>

<div class="container">

    <?php if(empty($data['user'])): ?>

        <h3 class="text-center mt-3">Необхідно налаштувати програму</h3>

        <div class="row justify-content-center mt-4">
            <div class="col" style="max-width: 600px">
                <form id="settings" class="form-floating" onsubmit="return false;">

                    <div class="form-floating mb-2">
                        <input class="form-control" id="clientSecret" type="text" name="client_secret" required="Має бути заповненим!"/>
                        <label for="clientSecret">CLIENT_SECRET...</label>
                    </div>

                    <div class="form-floating mb-2">
                        <input class="form-control" type="text" onchange="test_api(this)" name="api" id="api_prom" required="Має бути заповненим!"/>
                        <label for="api_prom">API токен від prom.ua...</label>
                    </div>

                    <div class="form-floating mb-2">
                        <select class="form-select" name="user_id" id="userId">
                            <option value="1" hidden=""></option>
                            <?= $data['option_list'] ?>
                        </select>
                        <label for="userId">Відповідальний за угоди</label>
                    </div>

                    <div class="form-floating mb-2">
                        <input class="form-control" type="text" name="site_name" id="name" required="Має бути заповненим!"/>
                        <label for="name">Бажане ім'я сайту PROM.UA в системі Бітрікс24...</label>
                    </div>

                    <input type="text" name="client_id" value="<?= $data['app_info']['CODE'] ?>" hidden>

                    <button type="submit" class="btn btn-light border border-info mt-3">Зберегти налаштування</button>

                </form>
            </div>
        </div>

    <?php else: ?>
    
        <h3 class="text-center mt-3">Активні сайти:</h3>
    
        <table class="table table-hover align-middle text-center mt-3">
    		<tr style='background-color:#cbf9ff'>
      			<th>№</th>
      			<th>Сайт</th>
                <th>Токен прому</th>
      			<th>Відповідальний</th>
      			<th>Дії</th>
      		</tr>
      		
      		<?php foreach($data['settings'] as $key => $item): ?>
      		
      		    <tr>
          		    <td><?= $key + 1 ?></td>
          		    <td><?= $item['site'] ?></td>
                    <td><?= $item['api'] ?></td>
          		    <td><?= $item['responsible'] ?></td>
          		    <td>
                        <span hidden><?= $item['id'] ?></span>
                        <span hidden><?= $item['responsible_id'] ?></span>
                        <span hidden><?= $item['user_id'] ?></span>
                        <button class="btn btn-light border-0 p-1" data-bs-toggle="modal" data-bs-target="#editModal" onclick="prepareEdit(this)">
                            <img data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Змінити запис" src="public/img/edit.png">
                        </button>
                        <button class="btn btn-light border-0 p-1 ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="prepareDelete(this)">
                            <img data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Видалити запис" src="public/img/trash.png">
                        </button>
                    </td>
          		</tr>
      		
      		<?php endforeach; ?>
      		
        </table>

        <button class="btn btn-light border border-info" data-bs-toggle="modal" data-bs-target="#createModal">Додати сайт...</button>

    <?php endif; ?>

</div>


<!-- Modal EDIT SETTINGS -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Зміна налаштувань</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id='edit' class="form-floating" onsubmit="return false;">

                <div class="modal-body">

                    <div class="form-floating mb-2">
                        <input class="form-control" type="text" onchange="test_api(this)" name="api" id="api_prom" required="Має бути заповненим!"/>
                        <label for="api_prom">API токен від prom.ua...</label>
                    </div>

                    <div class="form-floating mb-2">
                        <select class="form-select pb-2" name="user_id" id="userId">

                            <?= $data['option_list'] ?>

                        </select>
                        <label for="userId">Відповідальний за угоди</label>
                    </div>

                    <div class="form-floating mb-2">
                        <input class="form-control" type="text" name="site_name" id="name" required="Має бути заповненим!"/>
                        <label for="name">Бажане ім'я сайту PROM.UA в системі Бітрікс24...</label>
                    </div>

                    <input type="text" name="setting_id" hidden>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-light border border-info" onclick='update(this)'>Зберегти</button>
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть'>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Modal CREATE NEW SETTINGS -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createModalLabel">Додати налаштування</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id='create' class="form-floating" onsubmit="return false;">

                <div class="modal-body">

                    <div class="form-floating mb-2">
                        <input class="form-control" type="text" onchange="test_api(this)" name="api" id="api_prom" required="Має бути заповненим!"/>
                        <label for="api_prom">API токен від prom.ua...</label>
                    </div>

                    <div class="form-floating mb-2">
                        <select class="form-select pb-2" name="user_id" id="userId">

                            <?= $data['option_list'] ?>

                        </select>
                        <label for="userId">Відповідальний за угоди</label>
                    </div>

                    <div class="form-floating mb-2">
                        <input class="form-control" type="text" name="site_name" id="name" required="Має бути заповненим!"/>
                        <label for="name">Бажане ім'я сайту PROM.UA в системі Бітрікс24...</label>
                    </div>

                    <input type="text" name="user_id" value="<?= $data['user']['id'] ?>" hidden>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-light border border-info" onclick='create(this)'>Додати</button>
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть'>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Modal DELETE SETTINGS -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id='delete' class="form-floating" onsubmit="return false;">

                <div class="modal-body">

                    <div class="alert alert-danger text-center m-0" role="alert">
                        <h5>Ви точно бажаєте видалити даний запис?</h5>
                    </div>

                    <input type="text" name="setting_id" hidden>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light border border-info" onclick='del(this)'>Видалити</button>
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть'>
                </div>

            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

<script>

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    const settingsForm = document.querySelector('#settings');
    const editForm = document.querySelector('#edit');
    const createForm = document.querySelector('#create');
    const deleteForm = document.querySelector('#delete');

    function prepareEdit(elem){
        let responsible_id = elem.parentElement.children[1].innerText;
        let user_list = editForm[1].children;
        for (let item of user_list) {
            if(item.value === responsible_id){
                item.selected = true;
            }
        }
        editForm[0].value = elem.parentElement.parentElement.children[2].innerText;
        editForm[2].value = elem.parentElement.parentElement.children[1].innerText;
        editForm[3].value = elem.parentElement.children[0].innerText;
    }

    function prepareDelete(elem){
        deleteForm[0].value = elem.parentElement.children[0].innerText;
    }

    function update(btn){

        if(!editForm.reportValidity()){return false;}

        let span = document.createElement('span');
        span.className = 'spinner-border spinner-border-sm text-info ms-2';
        btn.append(span);

        const url = "public/fetch.php";

        let form = new FormData();
        form.append('responsible_id', editForm[1].value);
        form.append('responsible', editForm[1].selectedOptions[0].innerText);
        form.append('site', editForm[2].value);
        form.append('api', editForm[0].value);
        form.append('setting_id', editForm[3].value);
        form.append('method', 'update_settings');

        send(url, form).then(json => {
            if (json.data) {
                //console.dir(json.data);
                location.reload();
            } else {
                console.log('Помилка серверу!');
                return false;
            }
        });

    }

    function create(btn){

        if(!createForm.reportValidity()){return false;}

        let span = document.createElement('span');
        span.className = 'spinner-border spinner-border-sm text-info ms-2';
        btn.append(span);

        const url = "public/fetch.php";

        let form = new FormData();
        form.append('responsible_id', createForm[1].value);
        form.append('responsible', createForm[1].selectedOptions[0].innerText);
        form.append('site', createForm[2].value);
        form.append('api', createForm[0].value);
        form.append('user_id', createForm[3].value);
        form.append('method', 'create_settings');

        send(url, form).then(json => {
            if (json.data) {
                location.reload();
            } else {
                console.log('Помилка серверу!');
                return false;
            }
        });

    }

    function del(btn){

        let span = document.createElement('span');
        span.className = 'spinner-border spinner-border-sm text-info ms-2';
        btn.append(span);

        const url = "public/fetch.php";

        let form = new FormData();
        form.append('id', deleteForm[0].value);
        form.append('method', 'delete_settings');

        send(url, form).then(json => {
            if (json.data) {
                location.reload();
            } else {
                console.log('Помилка серверу!');
                return false;
            }
        });

    }




    
    if(settingsForm !== null){
        
        settingsForm.addEventListener('submit', () => {
            
            const url = "public/fetch.php";
        	
        	let form = new FormData();
        	form.append('client_secret', settingsForm[0].value);
        	form.append('api', settingsForm[1].value);
        	form.append('user_id', settingsForm[2].value);
        	form.append('responsible', settingsForm[2].selectedOptions[0].innerText);
        	form.append('site', settingsForm[3].value);
        	form.append('client_id', settingsForm[4].value);
        	form.append('auth', '<?= json_encode($data['auth']) ?>');
        	form.append('domain', '<?= $data['auth']['domain'] ?>');
        	form.append('method', 'save_settings');
        	
        	send(url, form).then(json => {
                if (json.data) {

                    location.reload();
                } else {
                    console.log('Помилка серверу!');
    		        return false;
                }
            });
            
       
        });
        
    }
    
    
    function test_api(elem){

        const url = "public/fetch.php";

        let form = new FormData();
        form.append('key', elem.value);
        form.append('method', 'test_api');

        send(url, form).then(json => {
            if (json.data) {
                elem.className = 'form-control border border-success';
                elem.nextElementSibling.className = 'text-success';
                elem.nextElementSibling.innerText = 'Даний ключ активний';
            } else {
                elem.className = 'form-control border border-danger';
                elem.nextElementSibling.className = 'text-danger';
                elem.nextElementSibling.innerText = 'Не вірний (не активний) ключ АПІ!!!';
                elem.value = null;
            }
        });

    }
    
    async function send(url, body = null) {
        
        if (body) {
            let response = await fetch(url, {
                method: 'POST',
                body: body,
                headers: {
                    //"Content-type": "application/json",
                    //"Authorization": "Bearer " + token
                }
            });
            return await response.json();
        } else {
            let response = await fetch(url, {method: 'GET'});
            return await response.json();
        }
    }
    
</script>

</body>
</html>
