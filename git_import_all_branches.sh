#!/bin/bash

# @package    Dev Tools
# @author     K Anderson <bitbashing@gmail.com>
# @license    Mozilla Public License (MPL)

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
