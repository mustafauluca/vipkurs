<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Bootstrap User Management Data Table</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style>
body {
    color: #566787;
    background: #f5f5f5;
    font-family: 'Varela Round', sans-serif;
    font-size: 13px;
}
.table-responsive {
    margin: 30px 0;
}
.table-wrapper {
    min-width: 1000px;
    background: #fff;
    padding: 20px 25px;
    border-radius: 3px;
    box-shadow: 0 1px 1px rgba(0,0,0,.05);
}
.table-title {
    padding-bottom: 15px;
    background: #299be4;
    color: #fff;
    padding: 16px 30px;
    margin: -20px -25px 10px;
    border-radius: 3px 3px 0 0;
}
.table-title h2 {
    margin: 5px 0 0;
    font-size: 24px;
}
.table-title .btn {
    color: #566787;
    float: right;
    font-size: 13px;
    background: #fff;
    border: none;
    min-width: 50px;
    border-radius: 2px;
    border: none;
    outline: none !important;
    margin-left: 10px;
}
.table-title .btn:hover, .table-title .btn:focus {
    color: #566787;
    background: #f2f2f2;
}
.table-title .btn i {
    float: left;
    font-size: 21px;
    margin-right: 5px;
}
.table-title .btn span {
    float: left;
    margin-top: 2px;
}
table.table tr th, table.table tr td {
    border-color: #e9e9e9;
    padding: 12px 15px;
    vertical-align: middle;
}
table.table tr th:first-child {
    width: 60px;
}
table.table tr th:last-child {
    width: 100px;
}
table.table-striped tbody tr:nth-of-type(odd) {
    background-color: #fcfcfc;
}
table.table-striped.table-hover tbody tr:hover {
    background: #f5f5f5;
}
table.table th i {
    font-size: 13px;
    margin: 0 5px;
    cursor: pointer;
}	
table.table td:last-child i {
    opacity: 0.9;
    font-size: 22px;
    margin: 0 5px;
}
table.table td a {
    font-weight: bold;
    color: #566787;
    display: inline-block;
    text-decoration: none;
}
table.table td a:hover {
    color: #2196F3;
}
table.table td a.settings {
    color: #2196F3;
}
table.table td a.delete {
    color: #F44336;
}
table.table td i {
    font-size: 19px;
}
table.table .avatar {
    border-radius: 50%;
    vertical-align: middle;
    margin-right: 10px;
}
.status {
    font-size: 30px;
    margin: 2px 2px 0 0;
    display: inline-block;
    vertical-align: middle;
    line-height: 10px;
}
.text-success {
    color: #10c469;
}
.text-info {
    color: #62c9e8;
}
.text-warning {
    color: #FFC107;
}
.text-danger {
    color: #ff5b5b;
}
.pagination {
    float: right;
    margin: 0 0 5px;
}
.pagination li a {
    border: none;
    font-size: 13px;
    min-width: 30px;
    min-height: 30px;
    color: #999;
    margin: 0 2px;
    line-height: 30px;
    border-radius: 2px !important;
    text-align: center;
    padding: 0 6px;
}
.pagination li a:hover {
    color: #666;
}	
.pagination li.active a, .pagination li.active a.page-link {
    background: #03A9F4;
}
.pagination li.active a:hover {        
    background: #0397d6;
}
.pagination li.disabled i {
    color: #ccc;
}
.pagination li i {
    font-size: 16px;
    padding-top: 6px
}
.hint-text {
    float: left;
    margin-top: 10px;
    font-size: 13px;
}
</style>

	
	<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();

    // Arama kutusu ile filtreleme
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Excel'e aktarma işlevi
    $("#exportExcel").on("click", function() {
        var table = document.getElementById("userTable");
        var wb = XLSX.utils.table_to_book(table, {sheet: "Kullanıcı Listesi"});
        XLSX.writeFile(wb, "Kullanici_Listesi.xlsx");
    });
});
</script>
	
	
</head>
<body>
<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-5">
                        <h2>Kullanıcı <b>Yönetimi</b></h2>
                    </div>
					
                    <div class="col-sm-7">
						<input type="text" id="searchInput" class="form-control" style="width: 200px; display: inline-block;" placeholder="Arama...">
                        <a href="?sf=users&md=add" class="btn btn-secondary"><i class="material-icons">&#xE147;</i> <span>Yeni Kullanıcı Ekle</span></a>
                        
                    </div>
                </div>
            </div>
			
			
			<?
$sf = isset($_GET['sf']) ? $_GET['sf'] : 'users';
$md = isset($_GET['md']) ? $_GET['md'] : 'table';
			?>

								
<? if($md=="add"):?>
			<?  include("useradd.php")?>
			
			<? elseif($md=="edit"):?>
			<? include("useredit.php")?>
			
			<? elseif($md=="delete"):?>
			<? include("userdelete.php")?>
			
			<? elseif($md=="unlock"):?>
			
			<? $id = $_GET['id']; $db->query("update kullanicilar set count = 0 where id = ?",[$id]);?>
			
			
			<? echo "<meta http-equiv='refresh' content='0;URL=index.php?sf=users'>"; ?>
			
			<? elseif($md=="table"):?>
			
			
			
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Adı</th>						
                        
                        <th>Yetkisi</th>
						  <th>Görevi</th>
                        <th>Durumu</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
					<? $i=1; foreach($db->getList("select * from kullanicilar where arsiv = 0 and firmaid = ? order by yetki desc, adi asc",[$ayarlar->id]) as $user):?>
                    <tr>
                        <td><?=$i++ ?></td>  <? if($user->foto<>''){$userfoto = $user->foto; } else { $userfoto = '../blank-profile.png';} ?>
                        <td><a href="#"><img src="../edusys/media/users/<?=$userfoto?>" class="avatar" width="48" alt="<?=$user->adi?> <?=$user->sadi?>"> <?=$user->adi?> <?=$user->sadi?> </a></td>
                     
						
						 <td>
							 
							     <?php if ($user->yetki == 10): ?>Müdür<?php endif; ?>
<?php if ($user->yetki == 9): ?>Bilgi İşlem<?php endif; ?>
<?php if ($user->yetki == 8): ?>Öğrenci İşleri<?php endif; ?>
<?php if ($user->yetki == 7): ?>Muhasebe<?php endif; ?>
<?php if ($user->yetki == 6): ?>Öğretmen<?php endif; ?>
<?php if ($user->yetki == 5): ?>Kayıt<?php endif; ?>
<?php if ($user->yetki == 4): ?>Sınav<?php endif; ?>
<?php if ($user->yetki == 3): ?>Kütüphane<?php endif; ?>
<?php if ($user->yetki == 2): ?>Mağaza<?php endif; ?>
<?php if ($user->yetki == 1): ?>Kantin<?php endif; ?>
                            
						
							 
                       </td>
						
                        <td><?=$user->gorev?></td>
                        <td>
							<? if($user->aktif==1):?>
							
							<? if($user->count<5):?>							
							 <span class="status text-success">&bull;</span> Aktif
							<? elseif($user->count==5):?>
							<span class="status text-danger">&bull;</span> Kilitli (<a href="?sf=users&md=unlock&id=<?=$user->id?>">Kilidi Aç</a>)
							<? endif;?>
							
							<? elseif($user->aktif==9):?>
							<span class="status text-danger">&bull;</span> Suspended
							<? elseif($user->aktif==0):?>
							<span class="status text-warning">&bull;</span> Inactive
						
						<? endif;?></td>
                        <td>
                            <a href="?sf=users&md=edit&id=<?=$user->id?>" class="settings" title="Düzenle" data-toggle="tooltip"><i class="material-icons">&#xE8B8;</i></a>
                            <a href="?sf=users&md=delete&id=<?=$user->id?>" class="delete" title="Kaldır" data-toggle="tooltip"><i class="material-icons">&#xE5C9;</i></a>
                        </td>
                    </tr>
					<? endforeach;?>
                    
                </tbody>
            </table>
            <div class="clearfix">
                <div class="hint-text">Showing <b>5</b> out of <b>25</b> entries</div>
                <ul class="pagination">
                    <li class="page-item disabled"><a href="#">Previous</a></li>
                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                    <li class="page-item active"><a href="#" class="page-link">3</a></li>
                    <li class="page-item"><a href="#" class="page-link">4</a></li>
                    <li class="page-item"><a href="#" class="page-link">5</a></li>
                    <li class="page-item"><a href="#" class="page-link">Next</a></li>
                </ul>
            </div>
			
			<? endif;?>
			
			
        </div>
    </div>
</div>     
</body>
</html>