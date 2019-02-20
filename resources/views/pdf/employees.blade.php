<style>
    .table{width:100%;max-width:100%;margin-bottom:1rem;background-color:transparent}
    .table td,.table th{padding:.75rem;vertical-align:top;border-top:1px solid #dee2e6}
    thead { display: table-header-group }
    tr { page-break-inside: avoid }
    .table thead th{font-size: 18px;text-align: left; vertical-align:bottom;border-bottom:2px solid #dee2e6}
    .table tbody+tbody{border-top:2px solid #dee2e6}
    .table .table{background-color:#f5f8fa}
    .table-sm td,.table-sm th{padding:.3rem}
    .table-striped tbody tr:nth-of-type(odd){background-color:rgba(0,0,0,.05)}
    .table-hover tbody tr:hover{background-color:rgba(0,0,0,.075)}
    h1,h2,h3,h4,h5,h6{margin-top:0;margin-bottom:.5rem}
</style>
<h1>Доступы в ЛК сотрудников</h1>
<table class="table table-sm table-striped">
    <thead>
        <tr>
            <th>Фамлия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Логин</th>
            <th>Пароль</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user[0]}}</td>
                <td>{{$user[1]}}</td>
                <td>{{$user[2]}}</td>
                <td>{{$user[3]}}</td>
                <td>{{$user[4]}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
