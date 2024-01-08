<nav class="navbar">
    <div class="nav-wrap">
        <h1>{{Auth::user()->name}} {{Auth::user()->surname}}</h1>
        <ul class="nav-collection">
            @if (Request::path() == '/')
            <li class="nav-items current-page" onclick="location.href='/';">
            @else
            <li class="nav-items" onclick="location.href='/';">
            @endif
                <div class="nav-icon"><i class="fa-solid fa-house"></i></div>
                <a href="/">Dashboard</a>
            </li>
            @if (Request::path() == 'budget')
            <li class="nav-items current-page" onclick="location.href='/budget';">
            @else
            <li class="nav-items" onclick="location.href='/budget';">
            @endif
                <div class="nav-icon"><i class="fa-solid fa-wallet"></i></i></div>
                <a href="/budget">Budget</a>
            </li>
            @if (Request::path() == 'add')
            <li class="nav-items current-page" onclick="location.href='/add';">
            @else
            <li class="nav-items" onclick="location.href='/add';">
            @endif
                <div class="nav-icon"><i class="fa-solid fa-plus"></i></i></div>
                <a href="/add">Add</a>
            </li>
            @if (Request::path() == 'reports')
            <li class="nav-items current-page" onclick="location.href='/reports';">
            @else
            <li class="nav-items" onclick="location.href='/reports';">
            @endif
                <div class="nav-icon"><i class="fa-solid fa-chart-column"></i></i></div>
                <a href="/reports">Reports</a>
            </li>
            @if (Request::path() == 'history')
            <li class="nav-items current-page" onclick="location.href='/history';">
            @else
            <li class="nav-items" onclick="location.href='/history';">
            @endif
                <div class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></i></div>
                <a href="/history">History</a>
            </li>
            @if (Request::path() == 'profile')
            <li class="nav-items current-page" onclick="location.href='/profile';">
            @else
            <li class="nav-items" onclick="location.href='/profile';">
            @endif
                <div class="nav-icon"><i class="fa-solid fa-user"></i></div>
                <a href="/profile">Profile</a>
            </li>
        </ul>
        <a href="/logout" class="btn btn-transparent" id="logout"><i class="fa-solid fa-power-off"></i> Log out</a>
    </div>
</nav>