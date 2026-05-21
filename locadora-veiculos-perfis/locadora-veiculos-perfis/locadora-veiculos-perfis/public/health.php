<?php
// Health check rápido para verificar se o Apache/PHP está servindo esta pasta
http_response_code(200);
echo "OK";
