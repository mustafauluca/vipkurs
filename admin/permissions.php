<? if(isset($_POST) and $db->getVar("select * from ayarlar where adminusername = ?",[$adminusername])>0):?>

<? //var_dump($_POST);?>

<? if(isset($_POST['islem']) and $_POST['islem']=='ekle'):?>
<? echo "<hr>EKLEME İŞLEMİ<hr>"?>
<?
$firma_id = $ayarlar->id;
$yetki = $_POST[ 'yetki' ];
$kullanici_id = $_POST[ 'kullanici_id' ];

// Yetki kontrolü ve validasyon
$insert = $db->query( "insert into yetkiler (firma_id, yetki, kullanici_id) VALUES (?,?,?)", [
	$firma_id, $yetki, $kullanici_id
] );

//$db->debug(); ?>


<? elseif(isset($_POST['islem']) and $_POST['islem']=='sil'):?>

<? echo "<hr>SİLME İŞLEMİ<hr>"?>

<?
$id = $_POST[ 'yetki' ];


$db->query( "Update yetkiler set aktif = 0, arsiv = 1 where id = ?", [ $id ] );

header("Location: ".$_SERVER['REQUEST_URI']);

//$db->debug();?>



<? endif; ?>



<? endif; ?>


<style>
	.card {
		font-size: 0.85rem;
		min-height: 250px;
		margin-bottom: 20px;
	}
	
	.list-group-item {
		padding: 0.25rem 0.5rem;
	}
	
	.form-select-sm {
		padding: 0.25rem 0.5rem;
		font-size: 0.8rem;
	}
	
	.btn-xs {
		padding: 0.1rem 0.35rem;
		font-size: 0.75rem;
		line-height: 1.2;
	}
</style>

<style>
	.bg-purple {
		background-color: #6f42c1 !important;
	}
	
	.bg-blue {
		background-color: #0d6efd !important;
	}
	
	.bg-teal {
		background-color: #20c997 !important;
	}
	
	.bg-orange {
		background-color: #fd7e14 !important;
	}
</style>


<div class="container-fluid mt-2">
	<div class="row g-3">
		<?php 
     $roller = [
    10 => ["Müdür", "bg-danger"],
    9 => ["Bilgi İşlem", "bg-primary"],
    8 => ["Öğrenci İşleri", "bg-success"],
    7 => ["Öğrenci Kayıt", "bg-info"],
    6 => ["Muhasebe", "bg-secondary"], // Warning kaldırıldı
    5 => ["Raporlar", "bg-dark"],
    4 => ["Sınav İşleri", "bg-purple"], // Özel renk (aşağıdaki CSS'e bakın)
    3 => ["Kütüphane", "bg-blue"], // Özel renk
    2 => ["Mağaza", "bg-teal"], // Özel renk
    1 => ["Kantin", "bg-orange"] // Özel renk
];
        
        foreach($roller as $yetkiDegeri => $rol): ?>
		<div class="col-lg  col-md-4 col-sm-6">
			<div class="card shadow-sm">
				<div class="card-header text-white py-2 <?=$rol[1]?>">
					<h6 class="mb-0 small fw-bold">
						<?= $rol[0] ?>-
						<?=$yetkiDegeri?>
					</h6>
				</div>

				<div class="card-body p-2">

					<div class="list-group small" style="max-height: 600px; overflow-y: auto;">
						<? foreach($db->getList("select * from kullanicilar where yetki = ?",[$yetkiDegeri])as $sabit):?>
						<div class="list-group-item p-1 px-2 d-flex justify-content-between align-items-center">
							<div class="text-truncate">
								<div class="fw-bold">
									<?= $sabit->adi ?>
									<?= $sabit->sadi ?>
									<small class="text-muted">(<?= $sabit->gorev ?>)</small>
								</div>
							</div>

						</div>
						<?php endforeach; ?>
					</div>






					<div class="list-group small" style="max-height: 600px; overflow-y: auto;">
						<?php 
                        $yetkililer = $db->getList("SELECT * FROM yetkiler WHERE firma_id = ? AND yetki = ? and aktif = 1", 
                                                 [$ayarlar->id, $yetkiDegeri]);
                        
                        foreach($yetkililer as $yetkili): 
                        $kullanici = $db->getRow("SELECT * FROM kullanicilar WHERE id = ?", [$yetkili->kullanici_id]);
                        ?>
						<div class="list-group-item p-1 px-2 d-flex justify-content-between align-items-center">
							<div class="text-truncate">
								<div class="fw-bold">
									<?= $kullanici->adi ?>
									<?= $kullanici->sadi ?>
									<small class="text-muted">(<?= $kullanici->gorev ?>)</small>
								</div>
							</div>
							<form method="post" action=""><input type="hidden" name="islem" value="sil">
								<input type="hidden" name="yetki" value="<?= $yetkili->id ?>">
								<button class="btn btn-xs btn-danger py-0" ?>×</button>
							</form>
						</div>
						<?php endforeach; ?>
					</div>


					<div class="input-group mt-2">
						<form method="post" action="">
							<input type="hidden" name="yetki" value="<?= $yetkiDegeri ?>">
							<input type="hidden" name="islem" value="ekle">


							<select class="form-select form-select-sm" id="select_<?= $yetkiDegeri ?>" name="kullanici_id">
								<option value="">Kullanıcı Seçin</option>



								<?php foreach($db->getList("SELECT * FROM kullanicilar where yetki <> 10 and yetki <> ?",[$yetkiDegeri]) as $kullanici): ?>
								
							
								
								<? $kontrol= $db->getVar("select count(*) from yetkiler where kullanici_id = ? and  aktif = 1 and yetki = ?",[ $kullanici->id, $yetkiDegeri]);?>
								<? //$db->debug();?>
								<? if($kontrol>1):?>
								
								<? else:?>
									<option value="<?= $kullanici->id ?>">
									<?=$kullanici->adi ;?>
									<?=$kullanici->sadi ;?> (<?=$kullanici->gorev ;?>)
										</option>
									
								<? endif;?>
								
								
								
								<?php endforeach; ?>


							</select>
							

							<button class="btn btn-sm btn-success py-0 px-2">+</button> </form>

					</div>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>

