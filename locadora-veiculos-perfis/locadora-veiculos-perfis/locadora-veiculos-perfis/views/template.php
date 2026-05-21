<?php
use Services\Auth;
use Models\LivroRaro;

$usuario = Auth::getUsuario();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/') . '/' ?>">
    <title>Livraria Locus — Locadora de Livros</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --cream:    #f5f0e8;
            --cream-dk: #ede6d6;
            --brown:    #3b2a1a;
            --brown-lt: #6b4f34;
            --gold:     #c8a84b;
            --gold-lt:  #e6d08a;
            --rust:     #8b3a2a;
            --green:    #2d6a4f;
        }
        body {
            background-color: var(--cream);
            font-family: 'Lato', sans-serif;
            font-weight: 300;
            color: var(--brown);
        }

        /* ── NAVBAR ─────────────────────────────────── */
        .top-bar {
            background: var(--brown);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }
        .brand {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-size: 1.4rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .brand span { color: #fff; font-size: .75rem; letter-spacing: .1em; text-transform: uppercase; font-family: 'Lato', sans-serif; font-weight: 300; }
        .user-pill {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: #fff;
            font-size: .9rem;
        }
        .username-badge {
            background: var(--gold);
            color: var(--brown);
            font-weight: 700;
            padding: .15rem .6rem;
            border-radius: 2px;
            font-size: .82rem;
            letter-spacing: .04em;
        }
        .btn-sair {
            background: transparent;
            border: 1px solid rgba(255,255,255,.35);
            color: #fff;
            border-radius: 2px;
            font-size: .82rem;
            padding: .3rem .75rem;
            display: flex;
            align-items: center;
            gap: .3rem;
            text-decoration: none;
            transition: background .2s, border-color .2s;
        }
        .btn-sair:hover { background: var(--rust); border-color: var(--rust); color: #fff; }

        /* ── LAYOUT ──────────────────────────────────── */
        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem 3rem; }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            color: var(--brown);
            border-bottom: 2px solid var(--gold);
            padding-bottom: .4rem;
            margin-bottom: 1.25rem;
        }

        /* ── CARDS ───────────────────────────────────── */
        .card {
            border: 1px solid #d9cfc0;
            border-radius: 2px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(59,42,26,.06);
        }
        .card-header {
            background: var(--cream-dk);
            border-bottom: 1px solid #d9cfc0;
            padding: .85rem 1.25rem;
        }
        .card-header h5 { font-family: 'Playfair Display', serif; font-size: 1rem; margin: 0; color: var(--brown); }

        /* ── FORM CONTROLS ───────────────────────────── */
        .form-label { font-size: .85rem; color: var(--brown-lt); font-weight: 400; margin-bottom: .25rem; }
        .form-control, .form-select {
            border-radius: 2px;
            border-color: #d4c9b8;
            background: var(--cream);
            color: var(--brown);
            font-size: .9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .15rem rgba(200,168,75,.25);
            background: #fff;
        }

        /* ── BUTTONS ─────────────────────────────────── */
        .btn-primary   { background: var(--brown);   border-color: var(--brown);   color: var(--cream); border-radius: 2px; }
        .btn-primary:hover { background: var(--brown-lt); border-color: var(--brown-lt); }
        .btn-info      { background: var(--gold);    border-color: var(--gold);    color: var(--brown); border-radius: 2px; }
        .btn-info:hover{ background: var(--gold-lt); border-color: var(--gold-lt); color: var(--brown); }
        .btn-warning   { background: #e07b35;        border-color: #e07b35;        color: #fff;         border-radius: 2px; }
        .btn-warning:hover { background: #c86828; border-color: #c86828; }
        .btn-danger    { background: var(--rust);    border-color: var(--rust);    color: #fff;         border-radius: 2px; }
        .btn-danger:hover { background: #6e2d1e; border-color: #6e2d1e; }

        /* ── TABLE ───────────────────────────────────── */
        .table { font-size: .88rem; color: var(--brown); }
        .table thead th { background: var(--cream-dk); border-bottom: 2px solid var(--gold); font-weight: 700; letter-spacing: .04em; font-size: .8rem; text-transform: uppercase; }
        .table tbody tr:hover { background: #fdf9f2; }
        .badge-disponivel { background: var(--green); color: #fff; border-radius: 2px; font-weight: 400; font-size: .78rem; padding: .25rem .6rem; }
        .badge-alugado    { background: var(--rust);  color: #fff; border-radius: 2px; font-weight: 400; font-size: .78rem; padding: .25rem .6rem; }
        .badge-raro       { background: var(--gold);  color: var(--brown); border-radius: 2px; font-weight: 700; font-size: .72rem; padding: .15rem .45rem; }

        /* ── ALERT ───────────────────────────────────── */
        .alert-locus {
            background: #fffbef;
            border: 1px solid var(--gold);
            border-left: 4px solid var(--gold);
            border-radius: 2px;
            color: var(--brown);
            font-size: .9rem;
        }

        /* ── ACTIONS ─────────────────────────────────── */
        .action-wrap  { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
        .days-input   { width: 62px !important; padding: .2rem .4rem; text-align: center; }

        @media (max-width: 576px) {
            .action-wrap { flex-direction: column; align-items: stretch; }
            .days-input  { width: 100% !important; }
        }
    </style>
</head>
<body>

<!-- ── TOP BAR ──────────────────────────────────────────── -->
<div class="top-bar">
    <a class="brand" href="#">
        <i class="bi bi-book-half"></i>
        Livraria Locus
        <span>/ Locadora</span>
    </a>
    <div class="user-pill">
        <i class="bi bi-person-circle" style="font-size:1.3rem; color:#ccc;"></i>
        Bem-vindo, <span class="username-badge"><?= htmlspecialchars($usuario['username']) ?></span>
        <a href="?logout=1" class="btn-sair"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
</div>

<!-- ── PAGE ─────────────────────────────────────────────── -->
<div class="page-wrap">

    <?php if ($mensagem): ?>
    <div class="alert alert-locus alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($mensagem) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- ── TOP ROW: FORMS ───────────────────────────────── -->
    <div class="row g-4 mb-4">

        <?php if (Auth::isAdmin()): ?>
        <!-- Adicionar Livro -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5><i class="bi bi-plus-circle me-2" style="color:var(--gold)"></i>Cadastrar Novo Livro</h5></div>
                <div class="card-body">
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" placeholder="Ex: O Cortiço" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Autor</label>
                            <input type="text" name="autor" class="form-control" placeholder="Ex: Aluísio Azevedo" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" class="form-control" placeholder="978-…" required>
                            </div>
                            <div class="col-5">
                                <label class="form-label">Tipo</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="Comum">Comum (R$ <?= number_format(DIARIA_COMUM, 2, ',', '.') ?>/dia)</option>
                                    <option value="Raro">Raro (R$ <?= number_format(DIARIA_RARO,  2, ',', '.') ?>/dia)</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="adicionar" class="btn btn-primary w-100">
                            <i class="bi bi-plus-lg me-1"></i>Cadastrar Livro
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Calcular Previsão -->
        <div class="col-md-<?= Auth::isAdmin() ? '6' : '6 offset-md-3' ?>">
            <div class="card h-100">
                <div class="card-header"><h5><i class="bi bi-calculator me-2" style="color:var(--gold)"></i>Simular Valor do Aluguel</h5></div>
                <div class="card-body">
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Livro</label>
                            <select name="tipo_calculo" class="form-select" required>
                                <option value="Comum">Comum — R$ <?= number_format(DIARIA_COMUM, 2, ',', '.') ?>/dia</option>
                                <option value="Raro">Raro — R$ <?= number_format(DIARIA_RARO, 2, ',', '.') ?>/dia</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade de Dias</label>
                            <input type="number" name="dias_calculo" class="form-control" value="7" min="1" required>
                        </div>
                        <button type="submit" name="calcular" class="btn btn-info w-100">
                            <i class="bi bi-search me-1"></i>Calcular Previsão
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TABLE ────────────────────────────────────────── -->
    <div class="card">
        <div class="card-header"><h5><i class="bi bi-journal-bookmarks me-2" style="color:var(--gold)"></i>Acervo da Locadora</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Status</th>
                            <?php if (Auth::isAdmin()): ?>
                            <th>Ações</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locadora->listarLivros() as $livro): ?>
                        <tr>
                            <td>
                                <?php if ($livro instanceof LivroRaro): ?>
                                    <span class="badge-raro">Raro</span>
                                <?php else: ?>
                                    <span style="color:var(--brown-lt); font-size:.82rem;">Comum</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight:400;"><?= htmlspecialchars($livro->getTitulo()) ?></td>
                            <td style="color:var(--brown-lt);"><?= htmlspecialchars($livro->getAutor()) ?></td>
                            <td style="font-size:.8rem; color:#9a8a78;"><?= htmlspecialchars($livro->getIsbn()) ?></td>
                            <td>
                                <?php if ($livro->isDisponivel()): ?>
                                    <span class="badge-disponivel"><i class="bi bi-check2 me-1"></i>Disponível</span>
                                <?php else: ?>
                                    <span class="badge-alugado"><i class="bi bi-clock me-1"></i>Alugado</span>
                                <?php endif; ?>
                            </td>
                            <?php if (Auth::isAdmin()): ?>
                            <td>
                                <div class="action-wrap">
                                    <form method="post" class="action-wrap">
                                        <input type="hidden" name="titulo" value="<?= htmlspecialchars($livro->getTitulo()) ?>">
                                        <input type="hidden" name="isbn"   value="<?= htmlspecialchars($livro->getIsbn()) ?>">

                                        <!-- Deletar -->
                                        <button type="submit" name="deletar" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash3"></i>
                                        </button>

                                        <?php if (!$livro->isDisponivel()): ?>
                                            <!-- Devolver -->
                                            <button type="submit" name="devolver" class="btn btn-warning btn-sm">
                                                <i class="bi bi-arrow-return-left me-1"></i>Devolver
                                            </button>
                                        <?php else: ?>
                                            <!-- Alugar -->
                                            <input type="number" name="dias" class="form-control days-input" value="7" min="1" title="Dias">
                                            <button type="submit" name="alugar" class="btn btn-primary btn-sm">
                                                <i class="bi bi-bag-check me-1"></i>Alugar
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (count($locadora->listarLivros()) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:#9a8a78; font-style:italic;">
                                Nenhum livro cadastrado no acervo.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /page-wrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>