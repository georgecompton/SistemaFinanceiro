<!DOCTYPE html>
<html lang="en">
  <head >
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema Financeiro | </title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <?php
        require_once 'init.php';
    
        /* SELEÇÃO DE REGISTRO DE DADOS */
        $where = null;
        $where2SQL = ' WHERE month(dataPublicacao) ='. date('m');
        $PDO = db_connect();
        
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            if(strtotime($_POST['dataInicio']) > strtotime($_POST['dataFinal']))
            {
                echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <strong>Erro!</strong> A Data Inicial não pode ser maior que a Data Final.
                      </div>';
            }
            elseif(strtotime($_POST['dataInicio']) == strtotime($_POST['dataFinal']))
            {
                echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                        </button>
                        <strong>Erro!</strong> A Data Inicial não pode igual a Data Final.
                      </div>';
            }
            else{
                $where = ' WHERE dataPublicacao BETWEEN :dataInicio AND :dataFinal ';
                $where2SQL = null;
            }
        }
        
        /* Conta Pagar */
        $sql = 'SELECT 
                    valor,
                    dataPublicacao,
                    Year(dataPublicacao) as ano
	        FROM tbcontapagar' .$where . $where2SQL. ' order by dataPublicacao ASC'; 
       
        $smtp = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $smtp->bindValue(':dataInicio', $_POST['dataInicio']); 
            $smtp->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $smtp->execute();
        
        $listaDiaPagar = array();
        
        while ($contaPagar = $smtp->fetch(PDO::FETCH_ASSOC)){
            $listaDiaPagar[] = $contaPagar;
        }
        
        $dataPublicacaoPagar = array_column($listaDiaPagar, 'dataPublicacao');
        $valorDiaPagar = array_column($listaDiaPagar, 'valor');
        $listaDiaPagar = array();
        
        foreach ($dataPublicacaoPagar as $date){
            $listaDiaPagar[] = dateConvert($date);
        }
        
        $dataPublicacaoPagar = $listaDiaPagar;
        
        $sql = 'SELECT 
                SUM(valor) - SUM(valorPago) as aPagar,
                CONCAT(ROUND((SUM(valorPago) / (SUM(valor) - SUM(valorPago))) * 100), "%") as porcetagem,
                SUM(valorPago) as valorPago,
                SUM(valor)  as valor
                FROM tbcontapagar' . $where . $where2SQL;
        
        $sm = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $sm->bindValue(':dataInicio', $_POST['dataInicio']); 
            $sm->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $sm->execute();
        
        $listaTotal;
        
        while ($contaPagar = $sm->fetch(PDO::FETCH_ASSOC)){
            $listaTotal = $contaPagar;
        }
        
        $aPagar = $listaTotal['aPagar'];
        $valorPago = $listaTotal['valorPago'];
        $porcetagemPago = $listaTotal['porcetagem'];
        $totalPagar = $listaTotal['valor'];
        
        /* Conta Receber */
        $sql = 'SELECT 
                    valor,
                    dataPublicacao,
                    Year(dataPublicacao) as ano
	        FROM tbcontareceber' .$where . $where2SQL. ' order by dataPublicacao ASC'; 
       
        $smtp = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $smtp->bindValue(':dataInicio', $_POST['dataInicio']); 
            $smtp->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $smtp->execute();
        
        $listaDiaReceber = array();
        
        while ($contaReceber = $smtp->fetch(PDO::FETCH_ASSOC)){
            $listaDiaReceber[] = $contaReceber;
        }
        
        $dataPublicacaoReceber = array_column($listaDiaReceber, 'dataPublicacao');
        $valorDiaReceber = array_column($listaDiaReceber, 'valor');
        $listaDiaReceber = array();
        
        foreach ($dataPublicacaoReceber as $date){
            $listaDiaReceber[] = dateConvert($date);
        }
        
        $dataPublicacaoReceber = $listaDiaReceber;
        
        $sql = 'SELECT 
                SUM(valor) - SUM(valorRecebido) as aReceber,
                CONCAT(ROUND((SUM(valorRecebido) / (SUM(valor) - SUM(valorRecebido))) * 100), "%") as porcetagem,
                SUM(valorRecebido) as valorRecebido,
                SUM(valor) as valor
                FROM tbcontareceber' . $where . $where2SQL;
        
        $sm = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $sm->bindValue(':dataInicio', $_POST['dataInicio']); 
            $sm->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $sm->execute();
        
        $listaTotal;
        
        while ($contaReceber = $sm->fetch(PDO::FETCH_ASSOC)){
            $listaTotal = $contaReceber;
        }
        
        $aReceber = $listaTotal['aReceber'];
        $valorRecebido = $listaTotal['valorRecebido'];
        $porcetagemRecebido = $listaTotal['porcetagem'];
        $totalReceber = $listaTotal['valor'];
        
        $porcetagemTotal = '0%';
        if($porcetagemPago !=null){
            $porcetagemP = str_replace('%', '', $porcetagemPago);
            $porcetagemTotal = 100 - $porcetagemP;
            $porcetagemTotal = $porcetagemTotal .'%';
        }
         
        $porcetagemRTotal = '0%';
        if($porcetagemRecebido !=null){
            $porcetagemR = str_replace('%', '', $porcetagemRecebido);
            $porcetagemRTotal = 100 - $porcetagemR;
            $porcetagemRTotal = $porcetagemRTotal .'%'; 
        }
        
        
    ?>  
      
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
                <a href="inicio.php" class="site_title"><i class="fa fa-paw"></i> <span>Financeiro</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="production/images/img.jpg" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Bem Vindo,</span>
                <h2>Visitante</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-home"></i> Início <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="protected/views/contaPagar/gerenciar.php">Conta Pagar</a></li>
                        <li><a href="protected/views/contaReceber/gerenciar.php">Conta Receber</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-bar-chart-o"></i> Dados de Apresentação <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="protected/views/contaPagar/dashboard.php">Conta Pagar</a></li>
                        <li><a href="protected/views/contaReceber/dashboard.php">Conta Receber</a></li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="index.php">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/img.jpg" alt="">Visitante
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li>
                      <a href="javascript:;">
                        <span class="badge bg-green pull-right"><?php //echo $porcetagemRecebido;?></span>
                        <span>% Recebido Conta Receber</span>
                      </a>  
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right"><?php //echo $porcetagemPago;?></span>
                        <span>% Pago Conta Pagar</span>
                      </a>
                    </li>
                    <li><a href="index.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-money"></i> Conta Pagar - A Pagar</span>
              <div class="count"  style="font-size: 30px;"><?php echo 'R$ '. number_format($aPagar, 2, ',', '.');?></div>
              <span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i><?php //echo $porcetagemTotal;?></i> Pagar</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-money"></i> Conta Pagar - Pago</span>
              <div class="count" style="font-size: 30px;"><?php echo 'R$ ' . number_format($valorPago, 2, ',', '.');?></div>
              <span class="count_bottom"><i class="red"><i class="fa fa-sort-asc"></i><?php //echo $porcetagemPago;?></i> Pago</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-balance-scale"></i> Faturamento</span>
              <div class="count green " style="font-size: 30px;"><?php $faturamento = $valorRecebido - $valorPago;  echo 'R$ '. number_format($faturamento, 2, ',', '.');?></div>
            </div>
            <div class="col-md-3 col-sm-1 col-xs-2 tile_stats_count">
              <span class="count_top"><i class="fa fa-money"></i> Conta Receber - A Receber</span>
              <div class="count" style="font-size: 30px;"><?php echo 'R$ '. number_format($aReceber, 2, ',', '.');?></div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-desc"></i><?php //echo $porcetagemRTotal;?></i> Receber</span>
              
            </div>
            <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-money"></i> Conta Receber - Recebido</span>
              <div class="count" style="font-size: 30px;"><?php echo 'R$ '. number_format($valorRecebido, 2, ',', '.');?></div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i><?php //echo $porcetagemRecebido;?></i> Recebido</span>
            </div>
          </div>
          <!-- /top tiles -->

          <div class="col-md-8 pull-right">
            <form method="post" class="form-inline">
                <div class="form-group">
                  <label for="dataInicio">Data Inicial</label>
                  <input id="dataInicio" name="dataInicio" class="form-control" type="date" required="required">
                </div>
                <div class="form-group">
                  <label for="dataFinal">Data Final</label>
                  <input type="date" id="dataFinal" name="dataFinal" class="form-control" required="required">
                </div>
                <button type="submit" class="btn btn-info">Pesquisar</button>
            </form>
            <br></br>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                
              <div class="dashboard_graph">
                  
                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Conta Pagar <small>Análise de Dados Temporal</small></h3>
                  </div>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                  <div class="x_content"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas id="lineChart" style="width: 300px; height: 150px;"></canvas>
                  </div>
                </div>
                  
                <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                  <div class="x_title">
                    <h2>Detalhamento</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-6">
                    
                    <div class="x_content">
                      <h4>Conta Pagar</h4>
                      <div class="widget_summary">
                        <div class="w_left w_25">
                          <span>Pagar</span>
                        </div>
                        <div class="w_center w_55">
                          <div class="progress">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $porcetagemTotal;?>;">
                              <span class="sr-only">60% Complete</span>
                            </div>
                          </div>
                        </div>
                          <div class="w_right w_20">
                              <span style="font-size: 11px;"><?php echo 'R$ ' .number_format($aPagar, 2, ',', '.');?></span>
                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="widget_summary">
                        <div class="w_left w_25">
                          <span>Pago</span>
                        </div>
                        <div class="w_center w_55">
                          <div class="progress">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $porcetagemPago;?>;">
                              <span class="sr-only">60% Complete</span>
                            </div>
                          </div>
                        </div>
                        <div class="w_right w_20">
                          <span style="font-size: 11px;"><?php echo 'R$ ' .number_format($valorPago, 2, ',', '.');?></span>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="widget_summary">
                        <div class="w_left w_25">
                          <span>Total</span>
                        </div>
                        <div class="w_center w_55">
                          <div class="progress">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                              <span class="sr-only">100% Complete</span>
                            </div>
                          </div>
                        </div>
                        <div class="w_right w_20">
                          <span style="font-size: 11px;"><?php echo 'R$ ' .number_format($totalPagar, 2, ',', '.');?></span>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
          <br />

          <div class="row">
              <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                
              <div class="dashboard_graph">
                  
                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Conta Receber <small>Análise de Dados Temporal</small></h3>
                  </div>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                  <div class="x_content"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas id="lineChart2" style="width: 300px; height: 150px;"></canvas>
                  </div>
                </div>
                  
                <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                  <div class="x_title">
                    <h2>Detalhamento</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-6">
                    
                    <div class="x_content">
                      <h4>Conta Receber</h4>
                      <div class="widget_summary">
                        <div class="w_left w_25">
                          <span>Receber</span>
                        </div>
                        <div class="w_center w_55">
                          <div class="progress">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $porcetagemRTotal;?>;">
                              <span class="sr-only">60% Complete</span>
                            </div>
                          </div>
                        </div>
                          <div class="w_right w_20">
                              <span style="font-size: 11px;"><?php echo 'R$ ' .number_format($aReceber, 2, ',', '.');?></span>
                        </div>
                        <div class="clearfix"></div>
                      </div>

                      <div class="widget_summary">
                        <div class="w_left w_25">
                          <span>Recebido</span>
                        </div>
                        <div class="w_center w_55">
                          <div class="progress">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $porcetagemRecebido;?>;">
                              <span class="sr-only">60% Complete</span>
                            </div>
                          </div>
                        </div>
                        <div class="w_right w_20">
                          <span style="font-size: 11px;"><?php echo 'R$ ' .number_format($valorRecebido, 2, ',', '.');?></span>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="widget_summary">
                        <div class="w_left w_25">
                          <span>Total</span>
                        </div>
                        <div class="w_center w_55">
                          <div class="progress">
                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                              <span class="sr-only">100% Complete</span>
                            </div>
                          </div>
                        </div>
                        <div class="w_right w_20">
                          <span><span style="font-size: 11px;"><?php echo 'R$ ' .number_format($totalReceber, 2, ',', '.');?></span></span>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
              
          </div>
  
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <a href="#">Sistema Financeiro</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="vendors/Flot/jquery.flot.js"></script>
    <script src="vendors/Flot/jquery.flot.pie.js"></script>
    <script src="vendors/Flot/jquery.flot.time.js"></script>
    <script src="vendors/Flot/jquery.flot.stack.js"></script>
    <script src="vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>
    
    <script type="text/javascript">
       $(function () {
            var f = document.getElementById("lineChart");
            new Chart(f, {
                type: "line",
                data: {
                    labels: <?php echo json_encode($dataPublicacaoPagar)?>,
                    datasets: [{
                        label: "Conta Pagar",
                        backgroundColor: "rgba(3, 88, 106, 0.3)",
                        borderColor: "rgba(3, 88, 106, 0.70)",
                        pointBorderColor: "rgba(3, 88, 106, 0.70)",
                        pointBackgroundColor: "rgba(3, 88, 106, 0.70)",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgba(151,187,205,1)",
                        pointBorderWidth: 1,
                        data: <?php echo json_encode($valorDiaPagar);?>
                    }]
                }
            })
            
            var f = document.getElementById("lineChart2");
            new Chart(f, {
                type: "line",
                data: {
                    labels: <?php echo json_encode($dataPublicacaoReceber)?>,
                    datasets: [{
                        label: "Conta Receber",
                        backgroundColor: "rgba(38, 185, 154, 0.31)",
                        borderColor: "rgba(38, 185, 154, 0.7)",
                        pointBorderColor: "rgba(38, 185, 154, 0.7)",
                        pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointBorderWidth: 1,
                        data: <?php echo json_encode($valorDiaReceber);?>
                    }]
                }
            })

            
       }); 
    </script>
    
	
  </body>
</html>
