should_precompile=0

if git diff-index --name-only HEAD | egrep '^resources/' >/dev/null ; then
  should_precompile=1
fi

if [[ ${should_precompile} -eq 1 ]]; then
  echo 'Compiling assets...'
  yarn prod
  git add themes/theme_brand_central/js
  git add themes/theme_brand_central/css
fi
