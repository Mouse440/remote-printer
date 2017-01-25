//
//  main.c
//  cups
//
//  Created by Duy Nguyen on 6/7/15.
//  Copyright (c) 2015 Duy Nguyen. All rights reserved.
//

#include <stdio.h>
#include <stdlib.h>
#include <cups/cups.h>

/*
 Compile using
 gcc -o cups `cups-config --cflags` cups.c `cups-config --libs`
 Note: Cups api can be found here http://www.cups.org/documentation.php/api-overview.html
 */
int main( int args, char *argv[])
//int main(void)
{
//    if( args != 2) /* argc should be 2 for correct execution */
//    {
//        printf("Error: No argument found.\n");
//        return (0);
//    } else {
    
//        printf( "You have entered: %s", argv[1] );
//        cups_dest_t *dest;
        int job_id = atoi(argv[1]);
        int num_jobs;
        cups_job_t *jobs;
        int i;
        ipp_jstate_t job_state = IPP_JOB_PENDING;
        
        while (job_state < IPP_JOB_STOPPED)
        {
            /* Get my jobs (1) with any state (-1) */
//            num_jobs = cupsGetJobs(&jobs, dest->name, 1, -1);
            num_jobs = cupsGetJobs(&jobs, NULL, 1, -1);

            /* Loop to find my job */
            job_state = IPP_JOB_COMPLETED;
            
            for (i = 0; i < num_jobs; i ++)
                if (jobs[i].id == job_id)
                {
                    job_state = jobs[i].state;
                    break;
                }
            
            /* Free the job array */
            cupsFreeJobs(num_jobs, jobs);
            
            /* Show the current state */
            switch (job_state)
            {
                case IPP_JOB_PENDING :
                    printf("Job %d is pending.\n", job_id);
                    break;
                case IPP_JOB_HELD :
                    printf("Job %d is held.\n", job_id);
                    break;
                case IPP_JOB_PROCESSING :
                    printf("Job %d is processing.\n", job_id);
                    break;
                case IPP_JOB_STOPPED :
                    printf("Job %d is stopped.\n", job_id);
                    break;
                case IPP_JOB_CANCELED :
                    printf("Job %d is canceled.\n", job_id);
                    break;
                case IPP_JOB_ABORTED :
                    printf("Job %d is aborted.\n", job_id);
                    break;
                case IPP_JOB_COMPLETED :
                    printf("Job %d is completed. %d\n", job_id, job_state);
                    break;
            }
            
            /* Sleep if the job is not finished */
            if (job_state < IPP_JOB_STOPPED)
                sleep(5);
        }
//    }
    
}
