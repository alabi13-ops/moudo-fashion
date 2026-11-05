<?php
/** Formulaire d'ajout / modification d'un article. @var ?array $p  @var ?array $old */
$isNew = $p === null;
$v = function (string $k, $def = '') use ($p, $old) {
    if (is_array($old) && array_key_exists($k, $old)) return $old[$k];
    return $p[$k] ?? $def;
};
$cols = [];
if (is_array($old) && isset($old['c_fr'])) {
    foreach ($old['c_fr'] as $i => $c) $cols[] = ['fr' => $c, 'en' => $old['c_en'][$i] ?? '', 'photo' => $old['c_photo'][$i] ?? ''];
} elseif ($p) {
    foreach ($p['couleurs'] ?? [] as $i => $c) $cols[] = ['fr' => $c, 'en' => $p['couleurs_en'][$i] ?? '', 'photo' => (($p['photos'][$i] ?? '') !== $p['image']) ? ($p['photos'][$i] ?? '') : ''];
}
$tagNow = $v('tag', '');
$stock = is_array($old) ? !empty($old['stock']) : (($p['stock'] ?? true) !== false);
$visible = is_array($old) ? !empty($old['visible']) : (($p['visible'] ?? true) !== false);
$taillesTxt = is_array($old) ? ($old['tailles'] ?? '') : implode(', ', $p['tailles'] ?? []);

admin_page($isNew ? 'Nouvel article' : 'Modifier', function () use ($p, $isNew, $v, $cols, $tagNow, $stock, $visible, $taillesTxt) { ?>
  <a class="back" href="/admin">← Mes articles</a>
  <h1><?= $isNew ? 'Nouvel article' : 'Modifier l\'article' ?></h1>
  <?= flash() ?>

  <form method="post" action="/admin/enregistrer" enctype="multipart/form-data" class="form" id="artForm">
    <?= csrf_champ() ?>
    <input type="hidden" name="id" value="<?= e($p['id'] ?? '') ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="10485760">

    <section class="card stack">
      <h2>Photo</h2>
      <label class="photo-pick">
        <img id="prev" <?= empty($p['image']) ? 'hidden' : 'src="' . e($p['image']) . '"' ?> alt="">
        <span class="photo-pick__empty" <?= empty($p['image']) ? '' : 'hidden' ?>>📷<br>Toucher pour choisir une photo</span>
        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" <?= $isNew ? 'required' : '' ?> data-preview="prev">
      </label>
      <p class="hint">JPG, PNG ou WebP, 10 Mo maximum. La photo est automatiquement réduite et optimisée.<?= $isNew ? '' : ' Laissez vide pour garder la photo actuelle.' ?></p>
    </section>

    <section class="card stack">
      <h2>Informations</h2>
      <label>Nom de l'article <span class="req">obligatoire</span><input name="nom" required maxlength="80" value="<?= e($v('nom')) ?>" placeholder="Ex. Jean baggy délavé"></label>
      <div class="row">
        <label>Rayon <span class="req">obligatoire</span>
          <select name="cat" required>
            <option value="">Choisir…</option>
            <?php foreach (CATEGORIES as $k => $c): ?><option value="<?= e($k) ?>" <?= $v('cat') === $k ? 'selected' : '' ?>><?= e($c['nom']) ?></option><?php endforeach; ?>
          </select>
        </label>
        <label>Prix (FCFA)<input name="prix" inputmode="numeric" pattern="[0-9 .]*" value="<?= e($v('prix') === null ? '' : $v('prix')) ?>" placeholder="Vide = prix sur WhatsApp"></label>
      </div>
      <label>Badge sur la photo
        <select name="tag">
          <?php foreach (BADGES as $k => $b): ?><option value="<?= e($k) ?>" <?= $tagNow === $b[0] ? 'selected' : '' ?>><?= $k === '' ? 'Aucun' : e($k) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label>Description<textarea name="desc" rows="3" maxlength="600" placeholder="Coupe, matière, comment le porter…"><?= e($v('desc')) ?></textarea></label>
      <label>Tailles de cet article <small>facultatif</small><input name="tailles" value="<?= e($taillesTxt) ?>" placeholder="Vide = tailles habituelles du rayon (ex. S, M, L, XL)"></label>
      <div class="toggles">
        <label class="switch"><input type="checkbox" name="stock" value="1" <?= $stock ? 'checked' : '' ?>><span></span>En stock</label>
        <label class="switch"><input type="checkbox" name="visible" value="1" <?= $visible ? 'checked' : '' ?>><span></span>Visible sur le site</label>
      </div>
    </section>

    <section class="card stack">
      <h2>Couleurs <small>facultatif</small></h2>
      <p class="hint">Ajoutez une ligne par couleur. Une photo par couleur est conseillée : elle s'affiche quand le client choisit la couleur, et c'est elle que vous voyez dans la commande WhatsApp.</p>
      <div id="colors" class="colors">
        <?php foreach ($cols as $i => $c): ?>
        <div class="color">
          <input name="c_fr[]" value="<?= e($c['fr']) ?>" placeholder="Couleur (ex. Noir)" maxlength="30" aria-label="Couleur">
          <input name="c_en[]" value="<?= e($c['en'] !== $c['fr'] ? $c['en'] : '') ?>" placeholder="En anglais (facultatif)" maxlength="30" aria-label="Couleur en anglais">
          <input type="hidden" name="c_photo[]" value="<?= e($c['photo']) ?>">
          <label class="color__photo"><img <?= $c['photo'] ? 'src="' . e($c['photo']) . '"' : 'hidden' ?> alt=""><span <?= $c['photo'] ? 'hidden' : '' ?>>+ Photo</span><input type="file" name="c_file[]" accept="image/jpeg,image/png,image/webp"></label>
          <button type="button" class="icon color__del" aria-label="Retirer cette couleur">✕</button>
        </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn--ghost" id="addColor">+ Ajouter une couleur</button>
    </section>

    <details class="card">
      <summary><h2>Version anglaise <small>facultatif</small></h2></summary>
      <div class="stack" style="margin-top:1rem">
        <p class="hint">Si vous laissez vide, le texte français s'affiche aussi sur la version anglaise du site.</p>
        <label>Nom en anglais<input name="nom_en" maxlength="80" value="<?= e($v('nom_en')) ?>" placeholder="Ex. Washed baggy jeans"></label>
        <label>Description en anglais<textarea name="desc_en" rows="3" maxlength="600"><?= e($v('desc_en')) ?></textarea></label>
      </div>
    </details>

    <div class="savebar">
      <button class="btn btn--primary btn--block" id="saveBtn"><?= $isNew ? 'Mettre en ligne' : 'Enregistrer' ?></button>
    </div>
  </form>

  <?php if (!$isNew): ?>
  <form method="post" action="/admin/supprimer" class="danger" onsubmit="return document.getElementById('confirmDel').checked">
    <?= csrf_champ() ?><input type="hidden" name="id" value="<?= e($p['id']) ?>">
    <h2>Supprimer cet article</h2>
    <label class="check"><input type="checkbox" id="confirmDel"> Je confirme vouloir supprimer « <?= e($p['nom']) ?> » définitivement.</label>
    <button class="btn btn--danger">Supprimer</button>
    <p class="hint">Astuce : pour le retirer temporairement, décochez plutôt « Visible sur le site ».</p>
  </form>
  <?php endif; ?>

  <template id="colorTpl">
    <div class="color">
      <input name="c_fr[]" placeholder="Couleur (ex. Noir)" maxlength="30" aria-label="Couleur">
      <input name="c_en[]" placeholder="En anglais (facultatif)" maxlength="30" aria-label="Couleur en anglais">
      <input type="hidden" name="c_photo[]" value="">
      <label class="color__photo"><img alt="" hidden><span>+ Photo</span><input type="file" name="c_file[]" accept="image/jpeg,image/png,image/webp"></label>
      <button type="button" class="icon color__del" aria-label="Retirer cette couleur">✕</button>
    </div>
  </template>

  <script>
    (function () {
      function preview(input, img, label) {
        var f = input.files && input.files[0]; if (!f) return;
        if (f.size > 10 * 1024 * 1024) { alertBox('Cette photo dépasse 10 Mo. Choisissez-en une plus légère.'); input.value = ''; return; }
        var url = URL.createObjectURL(f); img.src = url; img.hidden = false; if (label) label.hidden = true;
      }
      function alertBox(msg) {
        var d = document.createElement('div'); d.className = 'flash flash--err'; d.textContent = msg;
        document.querySelector('.wrap h1').after(d); d.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () { d.remove(); }, 6000);
      }
      var main = document.querySelector('[data-preview]');
      main.addEventListener('change', function () { preview(main, document.getElementById('prev'), document.querySelector('.photo-pick__empty')); });
      var box = document.getElementById('colors'), tpl = document.getElementById('colorTpl');
      document.getElementById('addColor').addEventListener('click', function () {
        box.appendChild(tpl.content.cloneNode(true)); box.lastElementChild.querySelector('input').focus();
      });
      box.addEventListener('click', function (e) { var b = e.target.closest('.color__del'); if (b) b.closest('.color').remove(); });
      box.addEventListener('change', function (e) {
        if (e.target.type !== 'file') return;
        var l = e.target.closest('.color__photo'); preview(e.target, l.querySelector('img'), l.querySelector('span'));
      });
      document.getElementById('artForm').addEventListener('submit', function () {
        var b = document.getElementById('saveBtn'); b.disabled = true; b.textContent = 'Envoi en cours…';
      });
    })();
  </script>
<?php });
