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
        
        NSMutableDictionary *months = [[NSMutableDictionary alloc] init];
        [months setValue:@"januari" forKey:@"01"];
        [months setValue:@"februari" forKey:@"02"];
        [months setValue:@"maart" forKey:@"03"];
        [months setValue:@"april" forKey:@"04"];
        [months setValue:@"mei" forKey:@"05"];
        [months setValue:@"juni" forKey:@"06"];
        [months setValue:@"juli" forKey:@"07"];
        [months setValue:@"augustus" forKey:@"08"];
        [months setValue:@"september" forKey:@"09"];
        [months setValue:@"oktober" forKey:@"10"];
        [months setValue:@"november" forKey:@"11"];
        [months setValue:@"december" forKey:@"12"];
        
        
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
            NSString *directoryName = [NSString stringWithFormat:@"%@ %@ %@", day, [[months objectForKey:month] description], year];
            NSLog(@"%@", directoryName);
        }
    }
    NSLog(@"done");
    return 0;
}

