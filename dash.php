<?php
// Conexão com o banco de dados
$conexao = new mysqli("localhost", "root", "", "extencao");

// Verifica se houve algum erro na conexão
if ($conexao->connect_error) {
    die("ERRO DE CONEXÃO COM O BANCO DE DADOS: " . $conexao->connect_error);
}

// Consultas SQL para métricas bancárias
$sqlTransacoesDiarias = "SELECT data, valor FROM transacoes_diarias";
$sqlTransacoesMensais = "SELECT mes, valor FROM transacoes_mensais";
$sqlTransacoesAnuais = "SELECT ano, valor FROM transacoes_anuais";
$sqlLimiteCartao = "SELECT usuario_id, limite FROM limites_cartao";
$sqlComprasTotais = "SELECT SUM(valor) as valor FROM compras";
$sqlSaldoTotal = "SELECT usuario_id, saldo FROM saldos";

// Executar as consultas SQL e armazenar os resultados em arrays
$resultTransacoesDiarias = $conexao->query($sqlTransacoesDiarias)->fetch_all(MYSQLI_ASSOC);
$resultTransacoesMensais = $conexao->query($sqlTransacoesMensais)->fetch_all(MYSQLI_ASSOC);
$resultTransacoesAnuais = $conexao->query($sqlTransacoesAnuais)->fetch_all(MYSQLI_ASSOC);
$resultLimiteCartao = $conexao->query($sqlLimiteCartao)->fetch_all(MYSQLI_ASSOC);
$resultComprasTotais = $conexao->query($sqlComprasTotais)->fetch_all(MYSQLI_ASSOC);
$resultSaldoTotal = $conexao->query($sqlSaldoTotal)->fetch_all(MYSQLI_ASSOC);

// Calcula os totais para os cards
$totalTransacoesDiarias = array_sum(array_column($resultTransacoesDiarias, 'valor'));
$totalTransacoesMensais = array_sum(array_column($resultTransacoesMensais, 'valor'));
$totalTransacoesAnuais = array_sum(array_column($resultTransacoesAnuais, 'valor'));
$totalLimiteCartao = array_sum(array_column($resultLimiteCartao, 'limite')) / count($resultLimiteCartao);
$totalCompras = $resultComprasTotais[0]['valor'];
$totalSaldo = array_sum(array_column($resultSaldoTotal, 'saldo'));

// Fechar conexão
$conexao->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>

<div class="grid-container">
    <!-- Header -->
    <header class="header">
        <div class="menu-icon" onclick="openSidebar()">
            <span class="material-icons-outlined">menu</span>Home
        </div>
        <div class="header-left" onclick="openFilterSidebar()">
            <span class="material-icons-outlined" onclick="openFilterSidebar()">filter_list</span>
        </div>
    </header>
    <!-- End Header -->

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-title">
            <div class="sidebar-brand">
                <span class="material-icons-outlined">dashboard</span>Admin - Dashboard
            </div>
        </div>
    </aside>
    <!-- End Sidebar -->

    <!-- Main -->
    <main class="main-container">
        <div class="main-title">
            <h2>DASHBOARD</h2>
        </div>

        <div class="main-cards">
            <div class="card">
                <div class="card-inner">
                    <h3>Total Transações Diárias</h3>
                    <span class="material-icons-outlined">attach_money</span>
                </div>
                <h1>R$ <?php echo number_format($totalTransacoesDiarias, 2, ',', '.'); ?></h1>
            </div>
            
            <div class="card">
                <div class="card-inner">
                    <h3>Total Transações Mensais</h3>
                    <span class="material-icons-outlined">attach_money</span>
                </div>
                <h1>R$ <?php echo number_format($totalTransacoesMensais, 2, ',', '.'); ?></h1>
            </div>
            
            <div class="card">
                <div class="card-inner">
                    <h3>Total Transações Anuais</h3>
                    <span class="material-icons-outlined">attach_money</span>
                </div>
                <h1>R$ <?php echo number_format($totalTransacoesAnuais, 2, ',', '.'); ?></h1>
            </div>

            <div class="card">
                <div class="card-inner">
                    <h3>Limite Médio do Cartão</h3>
                    <span class="material-icons-outlined">credit_card</span>
                </div>
                <h1>R$ <?php echo number_format($totalLimiteCartao, 2, ',', '.'); ?></h1>
            </div>

            <div class="card">
                <div class="card-inner">
                    <h3>Total de Compras</h3>
                    <span class="material-icons-outlined">shopping_cart</span>
                </div>
                <h1>R$ <?php echo number_format($totalCompras, 2, ',', '.'); ?></h1>
            </div>

            <div class="card">
                <div class="card-inner">
                    <h3>Saldo Total</h3>
                    <span class="material-icons-outlined">account_balance</span>
                </div>
                <h1>R$ <?php echo number_format($totalSaldo, 2, ',', '.'); ?></h1>
            </div>
        </div>

        <div class="charts">
            <div class="charts-card">
                <h2 class="chart-title">Transações Diárias</h2>
                <div id="transacoes_diarias_chart" style="width: 100%; height: 400px;"></div>
            </div>

            <div class="charts-card">
                <h2 class="chart-title">Transações Mensais</h2>
                <div id="transacoes_mensais_chart" style="width: 100%; height: 400px;"></div>
            </div>

            <div class="charts-card">
                <h2 class="chart-title">Transações Anuais</h2>
                <div id="transacoes_anuais_chart" style="width: 100%; height: 400px;"></div>
            </div>

            <div class="charts-card">
                <h2 class="chart-title">Limite do Cartão</h2>
                <div id="limite_cartao_chart" style="width: 100%; height: 400px;"></div>
            </div>

            <div class="charts-card">
                <h2 class="chart-title">Compras Totais</h2>
                <div id="compras_totais_chart" style="width: 100%; height: 400px;"></div>
            </div>

            <div class="charts-card">
                <h2 class="chart-title">Saldo Total</h2>
                <div id="saldo_total_chart" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </main>
    <!-- End Main -->
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
 // Função para criar gráfico de pizza
 function createPieChart(containerId, title, data) {
        Highcharts.chart(containerId, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y:.2f}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y:.2f}',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                name: 'Valor',
                colorByPoint: true,
                data: data
            }]
        });
    }

    // Função para criar gráfico de colunas
    function createColumnChart(containerId, title, categories, data) {
        Highcharts.chart(containerId, {
            chart: {
                type: 'column'
            },
            title: {
                text: title
            },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Valor (R$)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.2f} R$</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: data
        });
    }

    // Dados para os gráficos
    var transacoesDiariasData = <?php echo json_encode($resultTransacoesDiarias); ?>;
    var transacoesMensaisData = <?php echo json_encode($resultTransacoesMensais); ?>;
    var transacoesAnuaisData = <?php echo json_encode($resultTransacoesAnuais); ?>;
    var limiteCartaoData = <?php echo json_encode($resultLimiteCartao); ?>;
    var comprasTotaisData = <?php echo json_encode($resultComprasTotais); ?>;
    var saldoTotalData = <?php echo json_encode($resultSaldoTotal); ?>;

       // Criar gráficos
       createColumnChart('transacoes_diarias_chart', 'Transações Diárias', transacoesDiariasData.map(item => item.data), [{ name: 'Valor', data: transacoesDiariasData.map(item => parseFloat(item.valor)) }]);
    createColumnChart('transacoes_mensais_chart', 'Transações Mensais', transacoesMensaisData.map(item => item.mes), [{ name: 'Valor', data: transacoesMensaisData.map(item => parseFloat(item.valor)) }]);
    createColumnChart('transacoes_anuais_chart', 'Transações Anuais', transacoesAnuaisData.map(item => item.ano), [{ name: 'Valor', data: transacoesAnuaisData.map(item => parseFloat(item.valor)) }]);
    createPieChart('limite_cartao_chart', 'Limite do Cartão', limiteCartaoData.map(item => ({ name: `Usuário ${item.usuario_id}`, y: parseFloat(item.limite) })));
    createPieChart('compras_totais_chart', 'Compras Totais', [{ name: 'Total de Compras', y: parseFloat(comprasTotaisData[0].valor) }]);
    createPieChart('saldo_total_chart', 'Saldo Total', saldoTotalData.map(item => ({ name: `Usuário ${item.usuario_id}`, y: parseFloat(item.saldo) })));
</script>
</body>
</html>
