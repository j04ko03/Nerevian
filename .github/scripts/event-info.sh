#!/bin/bash

# Comprova quin tipus d'event ha disparat el workflow.
# La variable EVENT_NAME ve injectada des del yml via env:.
if [ "$EVENT_NAME" == "push" ]; then

  # Push directe a la branch — s'usen les variables de github.push context
  echo "event_type=Push directe" >> $GITHUB_OUTPUT
  echo "actor=$ACTOR" >> $GITHUB_OUTPUT

  # Agafa només la primera línia del missatge de commit (ignora el cos llarg)
  echo "commit_msg=$(echo "$COMMIT_MSG" | head -1)" >> $GITHUB_OUTPUT

  echo "commit_sha=$COMMIT_SHA" >> $GITHUB_OUTPUT

  # SHA curt (7 caràcters) per mostrar-lo a l'email de forma compacta
  echo "short_sha=$(echo "$COMMIT_SHA" | cut -c1-7)" >> $GITHUB_OUTPUT

else

  # Merge de PR — s'usen les variables de github.pull_request context
  echo "event_type=Merge de PR" >> $GITHUB_OUTPUT
  echo "actor=$PR_ACTOR" >> $GITHUB_OUTPUT

  # El missatge inclou el número i títol del PR per identificar-lo fàcilment
  echo "commit_msg=PR #$PR_NUMBER: $PR_TITLE" >> $GITHUB_OUTPUT

  echo "commit_sha=$PR_SHA" >> $GITHUB_OUTPUT
  echo "short_sha=$(echo "$PR_SHA" | cut -c1-7)" >> $GITHUB_OUTPUT

fi
# Tots els echo >> $GITHUB_OUTPUT escriuen variables d'output que els steps
# posteriors del workflow poden llegir amb ${{ steps.event-info.outputs.X }}