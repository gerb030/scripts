//
//  main.m
//  GroupFiles
//
//  Created by Gerben de Graaf on 1/27/13.
//  Copyright (c) 2013 Gerben de Graaf. All rights reserved.
//

#import <Foundation/Foundation.h>

Boolean fileExists(NSURL *path) {
    return [path checkResourceIsReachableAndReturnError:nil];
}

int main(int argc, const char * argv[])
{

    @autoreleasepool {
        
        // insert code here...
        NSString *path = @"/Users/gerb/testfotoos";
        NSURL *rootURL = [[NSURL alloc] initFileURLWithPath:path];
        if (!fileExists(rootURL)) {
            NSLog(@"Hey dude, the path you specified doesn't exist: %@.", rootURL);
            exit(1);
        }
        NSArray *keys = [NSArray arrayWithObjects:
                         NSURLIsDirectoryKey, NSURLIsPackageKey, NSURLLocalizedNameKey, nil];
        NSDirectoryEnumerator *enumerator = [[NSFileManager defaultManager]
                                             enumeratorAtURL:rootURL
                                             includingPropertiesForKeys:keys
                                             options:(NSDirectoryEnumerationSkipsPackageDescendants |
                                                      NSDirectoryEnumerationSkipsHiddenFiles)
                                             errorHandler:^(NSURL *url, NSError *error) {
                                                 return YES;
                                             }];
        for (NSURL *file in enumerator) {
            NSDictionary* fileAttribs = [[NSFileManager defaultManager] attributesOfItemAtPath:[file path] error:nil];
            NSDate *result = [fileAttribs fileCreationDate];
            NSString *day = [[result dateWithCalendarFormat:@"%d" timeZone:nil] description];
            NSString *month = [[result dateWithCalendarFormat:@"%m" timeZone:nil] description];
            NSString *year = [[result dateWithCalendarFormat:@"%Y" timeZone:nil] description];
            NSString *directoryName = [NSString stringWithFormat:@"%@ %@ %@", day, month, year];
            NSLog(@"%@", directoryName);
            NSLog(@"%i", [month intValue]);
        }
    }
    NSLog(@"done");
    return 0;
}

