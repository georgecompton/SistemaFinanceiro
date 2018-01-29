<?php
    require_once '../../../init.php';
    
    $table = 'tbcontapagar';
    
    $PDO = db_connect();
    $listaConta = array();
    
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
                    <strong>Erro!</strong> A Data Inicial não pode ser maior que a Data Final.
                  </div>';
        }
        else{
            
            $sql = "SELECT * FROM $table WHERE dataPublicacao BETWEEN :dataInicio AND :dataFinal ORDER BY dataPublicacao DESC";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(':dataInicio', $_POST['dataInicio']); 
            $stmt->bindValue(':dataFinal', $_POST['dataFinal']); 
            $stmt->execute();

            while ($contaPagar = $stmt->fetch(PDO::FETCH_ASSOC)){
                $listaConta[] = $contaPagar;
            }
        }
    }    
    else{
        $sql = "SELECT * FROM $table ORDER BY dataPublicacao DESC";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();

        while ($contaPagar = $stmt->fetch(PDO::FETCH_ASSOC)){
            $listaConta[] = $contaPagar;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema Financeiro! | </title>

    <!-- Bootstrap -->
    <link href="../../../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="../../../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../../../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
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
                <h3>Gerenciar <small>Conta a Pagar</small></h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <a class="btn btn-primary pull-right" href="form.php">Novo</a><br></br><br></br>  
                </div>
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

            <div class="clearfix"></div>

            <div class="row">
                
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Controle <small>Conta Pagar</small></h2>
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

                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Data </th>
                            <th class="column-title">Documento</th>
                            <th class="column-title">Descrição</th>
                            <th class="column-title">Data Vencimento</th>
                            <th class="column-title">Valor Lancamento</th>
                            <th class="column-title">Desconto</th>
                            <th class="column-title">Status</th>
                            <th class="column-title">Data Pagamento</th>
                            <th class="column-title">Valor Pago</th>
                            
                            
                            <th class="column-title no-link last"><span class="nobr">Action</span>
                            </th>
                            <th class="bulk-actions" colspan="7">
                              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                            </th>
                          </tr>
                        </thead>

                        <?php foreach($listaConta as $contaPagar): ?>
                        <tbody>
                            <tr class="even pointer">
                                <td><?php echo dateConvert($contaPagar['dataPublicacao']) ?></td>
                                <td><?php echo $contaPagar['documento'] ?></td>
                                <td><?php echo $contaPagar['descricao'] ?></td>
                                <td><?php echo dateConvert($contaPagar['dataVencimento']) ?></td>
                                <td><?php echo 'R$ '. number_format($contaPagar['valor'], 2, ',', '.'); ?></td>
                                <td><?php echo $contaPagar['desconto'] . ' %' ?></td>
                                <td><?php echo $contaPagar['status'] ?></td>
                                <td><?php echo dateConvert($contaPagar['dataPagamento']) ?></td>
                                <td><?php echo 'R$ '. number_format($contaPagar['valorPago'], 2, ',', '.'); ?></td>
                                <td>
                                    <div class="x_content">
                                        <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-info dropdown-toggle" type="button" aria-expanded="false">Ação <span class="caret"></span>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                          <li><?php echo "<a href='editar.php?id=" . $contaPagar['id'] . "'>Editar</a>"; ?>
                                          </li>
                                          <li><?php echo "<a href='../../controllers/ContaPagarController.php?metodo=makePayment&id=" . $contaPagar['id'] . "'>Pagar</a>"; ?>
                                          </li>
                                          <li><?php echo "<a href='../../controllers/ContaPagarController.php?metodo=delete&id=" . $contaPagar['id'] . "'>Deletar</a>"; ?>
                                          </li>
                                        </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <?php endforeach; ?>
                      </table>
                    </div>
							
						
                  </div>
                </div>
              </div>
              
              <div class="clearfix"></div>
              
            </div>
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
    <script src="../../../vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="../../../vendors/iCheck/icheck.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../../../build/js/custom.min.js"></script>
  </body>
</html>