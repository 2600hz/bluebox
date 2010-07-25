#!/bin/bash

lBranch="`git branch`"

git branch -r | while read rBranch ; do
	if [[ "${lBranch}" == *${rBranch:7}* ]]; then
   		echo "The remote branch $rBranch exists locally"
	elif [[ "${rBranch}" == *HEAD* ]]; then
		echo "Skipping remote branch ${rBranch}"
	else
		echo "Checkout remote branch ${rBranch} as ${rBranch:7}"
		git checkout -b ${rBranch:7} ${rBranch}
	fi
done
