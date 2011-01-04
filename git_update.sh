#!/bin/bash
trap "fStashPop" 0 1 2 5 15

fWelcome() {
    clear
    echo "========================================================"
    echo " ______  _      _     _ _______ ______  _______ _     _ "
    echo "(____  \(_)    (_)   (_|_______|____  \(_______|_)   (_)"
    echo " ____)  )_      _     _ _____   ____)  )_     _   ___   "
    echo "|  __  (| |    | |   | |  ___) |  __  (| |   | | |   |  "
    echo "| |__)  ) |____| |___| | |_____| |__)  ) |___| |/ / \ \ "
    echo "|______/|_______)_____/|_______)______/ \_____/|_|   |_|"
    echo
    echo "      - - - Our free software. Your next VoIP system!"
    echo "========================================================"
    printf '%56s' "Current Branch: $current_branch"
    echo
}

fStash() {
    echo
    echo "git stash"
    echo "---------------------------------------------------------"
    echo "This command saves your local modifications and reverts"
    echo "the working directory to match HEAD (the last commit"
    echo "revision). What this means is any locally changed files"
    echo "are temporarily stored then those modifications are"
    echo "undone, this ensures git can merge without conflict." 
    echo
    git stash || fStashFail
    return $?
}

fStashFail() {
    echo
    echo "!!! ERROR:LOCAL MODIFICATIONS COULD NOT BE STORED !!!"
    echo
    echo "No changes have been made to your source code, but we"
    echo "could not safely continue.  Ensure the source code is"
    echo "under GIT control or check the man page for git-stash."
    echo    
    exit 1
}

fFetch() {
    echo
    echo "git fetch"
    echo "---------------------------------------------------------"
    echo "Fetches named heads or tags from another repository,"
    echo "along with the objects necessary to complete them. This"
    echo "only retrieves updates, it does not apply them in any way"
    echo 
    git fetch || fFetchFail
    return $?
}

fFetchFail() {
    echo
    echo "       !!! ERROR:COULD NOT RETRIEVE UPDATES !!!"
    echo
    echo "No updates have been made to your source code, but we"
    echo "could not safely continue.  Ensure you have a working"
    echo "internet connection on this box and you can ping"
    echo "source.2600hz.org"
    echo    
    exit 1
}

fMerge() {
    echo
    echo "git merge origin/$current_branch"
    echo "---------------------------------------------------------"
    echo "Incorporates changes from the named commits (since the"
    echo "time their histories diverged from the current branch)"
    echo "into the current branch. This is the command the imports"
    echo "the updates retrieved by git fetch into the current source"
    echo 
    git merge origin/$current_branch || fMergeFail
    return $?
}

fMergeFail() {
    echo
    echo "       !!! ERROR:COULD NOT MERGE UPDATES !!!"
    echo
    echo "No updates have been made to your source code, but we"
    echo "could not safely continue.  Please resolve the git error"
    echo "above, use git status to get an idea what might be wrong"
    echo
    exit 1
}

fSubModule() {
    echo
    echo "git submodule sync/init/update"
    echo "---------------------------------------------------------"
    echo "Submodules allow foreign repositories to be embedded"
    echo "within a dedicated subdirectory of the source tree, always"
    echo "pointed at a particular commit.  This is used for external"
    echo "libraries that blue.box uses"
    echo 
    (git submodule sync && git submodule init && git submodule update  ) || fSubModuleFail
    return $?
}

fSubModuleFail() {
    echo
    echo "      !!! WARNING:COULD NOT UPDATE SUBMODULES !!!"
    echo 
    echo "blue.box does not directly rely on external libraries to"
    echo "operate, but some modules do use them.  You should work to"
    echo "determine what failed and how you can update it manually."
}

fStashPop() {
    echo
    echo "git stash pop"
    echo "---------------------------------------------------------"
    echo "Remove a single stashed state from the stash list and"
    echo "apply it on top of the current working tree state, i.e.,"
    echo "do the inverse operation of git stash save. The working"
    echo "directory must match the index."
    echo 
    retString="`git stash pop 2>&1`"
    retVal=$?

    echo "${retString}"
    if [[ "${retString}" == "Nothing to apply" ]]; then
        return 0
    fi

    if [ $retVal != 0 ]; then fStashPopFail; fi

    return $retVal
}

fStashPopFail() {
    echo
    echo "    !!! ERROR:COULD NOT RESTORE LOCAL CHANGES !!!"
    echo
    echo "Your configuration files, such as those for the database,"
    echo "are in git stash! You will need to solve the above issue"
    echo "and then run 'git stash apply'! Because your configuration"
    echo "is not applied you may be redirected to the blue.box"
    echo "installer, if you re-run it YOU WILL LOOSE DATA!" 
    echo
    echo "Applying the state can fail with conflicts; in this case,"
    echo "you need to resolve the conflicts by hand and call"
    echo "'git stash drop' manually once you are sure you have"
    echo "resolved the conflicts."
    echo
    echo "If there are conflicts with the stashed changes you"
    echo "could try running: git checkout stash@{0} {dir_of_file}"
    echo    
    exit 1
}

current_branch="`git status | grep 'On branch' | cut -d ' ' -f 4`"

clear

fWelcome

fStash && fFetch && fMerge && fSubModule
