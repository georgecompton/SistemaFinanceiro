<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema Financeiro</title>

    <!-- Bootstrap -->
    <link href="../../../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../../vendors/nprogress/nprogress.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../../../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
      
    <?php
        require_once '../../../init.php';
    
        /* SELEÇÃO DE REGISTRO DE DADOS */
        $table = 'tbcontapagar';
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
                echo 'A data 1 é igual a data 2.';
            }
            else{
                $where = ' WHERE dataPublicacao BETWEEN :dataInicio AND :dataFinal ';
                $where2SQL = null;
            }
        }
        
       /* 1° SQL */ 
       $sql = 'SELECT 
                    if(Month(dataPublicacao)=1,"Janeiro",
                    if(Month(dataPublicacao)=2,"Fevereiro",
                    if(Month(dataPublicacao)=3,"Março",
                    if(Month(dataPublicacao)=4,"Abril",
                    if(Month(dataPublicacao)=5,"Maio",
                    if(Month(dataPublicacao)=6,"Junho",
                    if(Month(dataPublicacao)=7,"Julho",
                    if(Month(dataPublicacao)=8,"Agosto",
                    if(Month(dataPublicacao)=9,"Setembro",
                    if(Month(dataPublicacao)=10,"Outubro",
                    if(Month(dataPublicacao)=11,"Novembro","Dezembro"))))))))))) as mes,
                    SUM(valor) as valor,
                    ROUND((SUM(valor) / c.total) * 100) as porcetagem,
                    Year(dataPublicacao) as ano
                FROM ' .$table. ' 
                JOIN (SELECT sum(valor) AS total FROM ' .$table. ') AS c    
                    ' .$where . '
                    GROUP BY mes
		    HAVING SUM(valor)
                    order by dataPublicacao ASC';
        
        $stmt = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $stmt->bindValue(':dataInicio', $_POST['dataInicio']); 
            $stmt->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        
        $stmt->execute();
        
        $listaMes = array();
        while ($contaPagar = $stmt->fetch(PDO::FETCH_ASSOC)){
            $listaMes[] = $contaPagar;
        }
        
        $mes = array_column($listaMes, 'mes');
        $ano = array_column($listaMes, 'ano');
        $valor = array_column($listaMes, 'valor');
        $porcetagem = array_column($listaMes, 'porcetagem');
        
        /* 2° SQL */
        $sql = 'SELECT 
                    valor,
                    dataPublicacao,
                    Year(dataPublicacao) as ano
                FROM ' .$table. $where . $where2SQL. ' order by dataPublicacao ASC'; 
       
        $smtp = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $smtp->bindValue(':dataInicio', $_POST['dataInicio']); 
            $smtp->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $smtp->execute();
        
        $listaDia = array();
        
        while ($contaPagar = $smtp->fetch(PDO::FETCH_ASSOC)){
            $listaDia[] = $contaPagar;
        }
        
        $dataPublicacaoDia = array_column($listaDia, 'dataPublicacao');
        $valorDia = array_column($listaDia, 'valor');
        
        $listaDia = array();
        foreach ($dataPublicacaoDia as $date){
            $listaDia[] = dateConvert($date);
        }
        
        $dataPublicacaoDia = $listaDia;
        
        /* 3° SQL */
        $sql = 'SELECT 
                    sum(valor) as valor,
                    Year(dataPublicacao) as ano,
                    ROUND((SUM(valor) / c.total) * 100) as porcetagem
                FROM ' .$table. '
                JOIN (SELECT sum(valor) AS total FROM ' .$table. ') AS c
                ' .$where . '    
                Group by ano 
                order by ano ASC';
        
        $smta = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $smta->bindValue(':dataInicio', $_POST['dataInicio']); 
            $smta->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $smta->execute();
        
        $listaAno = array();
        
        while ($contaPagar = $smta->fetch(PDO::FETCH_ASSOC)){
            $listaAno[] = $contaPagar;
        }
        
        $dataAno = array_column($listaAno, 'ano');
        $valorAno = array_column($listaAno, 'valor');
        $porcetagemAno = array_column($listaAno, 'porcetagem');
        
        /* 4° SQL */
        $sql = 'SELECT sum(valor) - sum(valorPago) as valorTotal,
                valorPago
                FROM ' .$table. $where . $where2SQL;
        
        $sm = $PDO->prepare($sql);
        if(!empty($_POST['dataInicio']) & !empty($_POST['dataFinal'])){
            $sm->bindValue(':dataInicio', $_POST['dataInicio']); 
            $sm->bindValue(':dataFinal', $_POST['dataFinal']);
        }
        $sm->execute();
        
        $listaTotal = array();
        
        while ($contaPagar = $sm->fetch(PDO::FETCH_ASSOC)){
            $listaTotal[] = $contaPagar;
        }
        
        $valorTotal = array_column($listaTotal, 'valorTotal');
        $valorPagoTotal = array_column($listaTotal, 'valorPago');
        
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
        
        $valorTotal = $listaTotal['valorTotal'];
        $valorPagoTotal = $listaTotal['valorPago'];
        
    ?>  
    
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="../../../inicio.php" class="site_title"><i class="fa fa-paw"></i> <span>Financeiro!</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="../../../production/images/img.jpg" alt="..." class="img-circle profile_img">
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
                        <li><a href="gerenciar.php">Conta Pagar</a></li>
                        <li><a href="../contaReceber/gerenciar.php">Conta Receber</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-bar-chart-o"></i> Dados de Apresentação <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="dashboard.php">Conta Pagar</a></li>
                        <li><a href="../contaReceber/dashboard.php">Conta Receber</a></li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->
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
                    
                    <li><a href="../../../index.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Análise Temporal <small>Conta Pagar</small></h3>
              </div>

              <div class="x_content">  
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
              </div>
            </div>
          </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Demostrativo (R$) Total<small>Diário</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                      <li></li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="lineChart"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Demostrativo (R$) Total<small>Mensal</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                      <li></li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="mybarChart"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Demostrativo (%) Total <small>Mensal</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                      <li></li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="canvasDoughnut"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-md-4 tile">
                <span>Total A Pagar</span>
                <h2>R$ <?php echo (!empty($valorTotal)? number_format($valorTotal, 2, ',', '.')  : '');?></h2>
                <span>Total Pago</span>
                <h2>R$ <?php echo (!empty($valorPagoTotal) ? number_format($valorPagoTotal, 2, ',', '.') : '');?></h2>
              </div>
                
              <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Demostrativo (%)<small>Ano</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="polarArea"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Demostrativo (R$) <small>Ano</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <canvas id="pieChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <br />
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
              <a href="../../../inicio.php">Sistema Financeiro</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../../../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../../../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="../../../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="../../../vendors/Chart.js/dist/Chart.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../../../build/js/custom.min.js"></script>
    
    <script type="text/javascript">
       $(function () {
           
            new Chart(document.getElementById("lineChart"), {
                type: "line",
                data: {
                    labels: <?php echo json_encode($dataPublicacaoDia)?>,
                    
                    datasets: [{
                        label: "Valor(R$)",
                        backgroundColor: "rgba(3, 88, 106, 0.3)",
                        borderColor: "rgba(3, 88, 106, 0.70)",
                        pointBorderColor: "rgba(3, 88, 106, 0.70)",
                        pointBackgroundColor: "rgba(3, 88, 106, 0.70)",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgba(151,187,205,1)",
                        pointBorderWidth: 1,
                        data: <?php echo json_encode($valorDia)?>
                    }]
                }
            })
            
            new Chart(document.getElementById("mybarChart"), {
                 type: "bar",
                 data: {
                     labels: <?php echo json_encode($mes)?>,
                     datasets: [{
                         label: "# Valor Total (R$)",
                         backgroundColor: "#26B99A",
                         data: <?php echo json_encode($valor)?>
                     }]
                 },
                 options: {
                     scales: {
                         yAxes: [{
                             ticks: {
                                 beginAtZero: !0
                             }
                         }]
                     }
                 }
            })
            
            var f = document.getElementById("canvasDoughnut"),
                i = {
                    labels: <?php echo json_encode($mes)?>,
                    datasets: [{
                        data: <?php echo json_encode($porcetagem)?>,
                        backgroundColor: ["#B370CF", "#26B99A", "#3498DB", "#455C73", "#9B59B6", , "#ff4500", "#BDC3C7"],
                        hoverBackgroundColor: ["#34495E", "#B370CF", "#CFD4D8", "#36CAAB", "#49A9EA", "#ff4500"]
                    }]
                };
                
            new Chart(f, {
                type: "doughnut",
                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                data: i
            })
            
            var f = document.getElementById("pieChart"),
                i = {
                    datasets: [{
                        data: <?php echo json_encode($valorAno)?>,
                        backgroundColor: ["#26B99A", "#3498DB", "#ff4500", "#BDC3C7", "#9B59B6", "#455C73"],
                        label: "My dataset"
                    }],
                    labels: <?php echo json_encode($dataAno)?>
                };
            new Chart(f, {
                data: i,
                type: "pie",
                otpions: {
                    legend: !1
                }
            })
            
            var f = document.getElementById("polarArea"),
                i = {
                    datasets: [{
                        data: <?php echo json_encode($porcetagemAno)?>,
                        backgroundColor: ["#BDC3C7", "#ff4500", "#455C73", "#26B99A", "#3498DB", "#9B59B6" ],
                        label: "My dataset"
                    }],
                    labels: <?php echo json_encode($dataAno)?>
                };
            new Chart(f, {
                data: i,
                type: "polarArea",
                options: {
                    scale: {
                        ticks: {
                            beginAtZero: !0
                        }
                    }
                }
            })
            
       }); 
    </script>
    
  </body>
</html>