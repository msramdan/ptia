</div>
<script src="{{ asset('assets/jquery/js/jquery.min.js') }}"></script>
<script src="{{ asset('mazer') }}/static/js/components/dark.js"></script>
<script src="{{ asset('mazer') }}/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="{{ asset('mazer') }}/compiled/js/app.js"></script>
{{-- socket IO --}}
<script src="{{ asset('libvelixs') }}/client-dist/socket.io.js"></script>
<script>
    let limit_attempts = {{ config('app.attemp_socket') }};
    let attempts = 0;
    @if (config('app.socket_default'))
        const socket = io();
    @else
        const socket = io("{{ config('app.base_node') }}", {
            transports: ['websocket']
        });
    @endif

    socket.on('connect_error', () => {
        $("#server-status").html(
            '<li class="sidebar-item" style="background-color: #f8d7da; color: #721c24; border-radius: 5px;"><a class="sidebar-link" href="#" style="color: #721c24; text-decoration: none;"><span style="margin-left: 5px;"><b><i style="color:#721c24" class="fa fa-server" aria-hidden="true"></i> Server Wa - Disconnected</b></span></a></li>'
        );
        $(".status-connection").html(
            `<span class="badge rounded-pill bg-label-secondary"><span style="font-size: 1.05rem;" class="ti ti-plug-connected-x"></span> -</span>`
        )
        attempts++;
        if (attempts >= limit_attempts) {
            socket.disconnect();
        }
    });

    socket.on('connect', () => {
        $("#server-status").html(
            '<li class="sidebar-item" style="background-color: #d4edda; color: #155724; border-radius: 5px;"><a class="sidebar-link" href="#" style="color: #155724; text-decoration: none;"><span style="margin-left: 5px;"><b><i style="color:#155724" class="fa fa-server" aria-hidden="true"></i> Server Wa - Connected</b></span></a></li>'
        );
        attempts = 0;
    });
</script>
<script src="{{ asset('mazer/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
@stack('js')
</body>

</html>
