#include <iostream>
#include <string>
#include <ctime>
#include <sstream>
#include <windows.h>

void main(int argc, const char * argv[])
{
    
    time_t currentTime;
    struct tm *localTime;

    time( &currentTime );                   // Get the current time
    localTime = localtime( &currentTime );  // Convert the current time to the local time

    int hour = (localTime->tm_hour)*100;
    std::stringstream ss;
    ss << hour;

    std::string str1("php autoUpdate.php ");                                    
    std::string str_update_time = ss.str();

    std::string command = str1 + str_update_time;
    std::cout << command <<std::endl;
    system(command.c_str());

}
