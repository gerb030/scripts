#!/usr/bin/perl

# Script to move image files to a corresponding directory containing the date as directory name
#
#  Created by Gerben de Graaf on 08 05 2008
#  Copyright 2008 Gerben de Graaf. All rights reserved.
#    This program is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 3 of the License, or any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

# TO DO
# - protect : directories sometimes contain spaces

#my $exeDir;
    # get exe directory
  #  $exeDir = ($0 =~ /(.*)[\\\/]/) ? $1 : '.';
    # add lib directory at start of include path
BEGIN {
	unshift @INC, "/usr/bin/lib";
	@months = ("januari", "februari", "maart", "april", "mei", "juni", "juli",
    "augustus", "september", "oktober", "november", "december");
}

#use Image::ExifTool;
use File::stat;

$workDir = $ARGV[0];
#my $exifTool = new Image::ExifTool;

opendir(HANDLE, $workDir) or die "Couldn't open $workDir : $!";
while (defined ($filename = readdir(HANDLE)) ) {
	if ($filename =~ m/\.jp(e){0,1}g$/i) {
		# it's a jpeg file
		$inode = stat("$workDir$filename") or die "Cannot stat $workDir$filename\n";
		$mtime = $inode->mtime;
		($d, $m, $y) = (localtime($mtime))[3..5];
		if ($d =~ m/^\d{1}$/) {
			$d =~ s/^(\d{1})$/0$1/;
		}
		$month = @months[$m];
		$year = $y+1900;
		$monthDirName = "$month";
		$dayDirName = "$d\\ $month\\ $year\\ -\\ ";
		if(!opendir(SUBHANDLE, "$workDir$monthDirName")) {
			system("mkdir $workDir$monthDirName"); 
			#print "Created month directory $monthDirName\n";
		}
		if(!opendir(SUBSUBHANDLE, "$workDir$monthDirName/$dayDirName")) { 
			system("mkdir $workDir$monthDirName/$dayDirName"); 
			#print "Created day directory $dayDirName\n";
		}
		system("mv $workDir$filename $workDir$monthDirName/$dayDirName"); 
		print " - moved file $filename.\n";
	}
} 

#$mtime = (stat($file));
#@f = (stat($file))[1..5];#(localtime)[3..5]; # grabs day/month/year values
#printf "%d-%d-%d\n", $f[1] +1, $f[0], $f[2];

#print ($^T-$mtime); 
#print "\nok\n";
