#!/usr/bin/env python3
import os,sys,zipfile
out=sys.argv[1]
excluded={'.git','.github','.husky','.storybook','.vscode','.claude','.cursor','.swc','node_modules','build','.env','.idea','tests','cypress','scripts','stories'}
excluded_files={'tutor.zip','.env.example','.travis.yml','.lintstagedrc','.npmrc','.prettierrc','package.json','pnpm-lock.yaml','gulpfile.mjs','rspack.config.mjs','rsbuild.config.ts','cypress.config.ts','eslint.config.mjs','phpunit.xml','phpunit.xml.dist','phpcs.xml','phpcs.xml.dist','phpstan.neon','pnpm-workspace.yaml','purgecss.config.mjs','tsconfig.json'}
with zipfile.ZipFile(out,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as z:
  for base,dirs,files in os.walk('.'):
    dirs[:]=sorted(d for d in dirs if d not in excluded)
    for name in sorted(files):
      path=os.path.join(base,name).removeprefix('./')
      if name in excluded_files or name.endswith('.zip') or '/fixtures/' in path or path.startswith('assets/src/') or path.startswith('assets/core/'): continue
      info=zipfile.ZipInfo('tutor/'+path,(2020,1,1,0,0,0)); info.external_attr=(os.stat(path).st_mode&0xffff)<<16
      with open(path,'rb') as f:z.writestr(info,f.read(),compress_type=zipfile.ZIP_DEFLATED,compresslevel=9)
