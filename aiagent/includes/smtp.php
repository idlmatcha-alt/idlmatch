<?php
declare(strict_types=1);

final class SmtpMailer {
    private $socket;
    private array $cfg;
    public function __construct(array $cfg) { $this->cfg = $cfg; }
    private function read(): string {
        $data=''; while (!feof($this->socket)) { $line=fgets($this->socket, 515); if ($line===false) break; $data.=$line; if (preg_match('/^\d{3} /',$line)) break; }
        return $data;
    }
    private function cmd(string $cmd, array $ok): string {
        fwrite($this->socket, $cmd."\r\n"); $resp=$this->read(); $code=(int)substr($resp,0,3);
        if (!in_array($code,$ok,true)) throw new RuntimeException('SMTP error: '.$resp);
        return $resp;
    }
    public function send(string $to, string $subject, string $html, ?string $text=null): void {
        $host=$this->cfg['host']; $port=(int)$this->cfg['port']; $enc=$this->cfg['encryption'] ?? 'tls';
        $transport = $enc==='ssl' ? 'ssl://' : 'tcp://';
        $this->socket=@stream_socket_client($transport.$host.':'.$port,$errno,$errstr,20,STREAM_CLIENT_CONNECT);
        if (!$this->socket) throw new RuntimeException("SMTP connect failed: $errstr ($errno)");
        stream_set_timeout($this->socket, 25); $this->read();
        $this->cmd('EHLO idlmatch.com',[250]);
        if ($enc==='tls') {
            $this->cmd('STARTTLS',[220]);
            if (!stream_socket_enable_crypto($this->socket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT)) throw new RuntimeException('STARTTLS failed');
            $this->cmd('EHLO idlmatch.com',[250]);
        }
        if (!empty($this->cfg['username'])) {
            $this->cmd('AUTH LOGIN',[334]);
            $this->cmd(base64_encode($this->cfg['username']),[334]);
            $this->cmd(base64_encode($this->cfg['password']),[235]);
        }
        $from=$this->cfg['from_email']; $name=$this->cfg['from_name'] ?? '';
        $fromHeader = $name ? '=?UTF-8?B?'.base64_encode($name).'?= <'.$from.'>' : '<'.$from.'>';
        $this->cmd('MAIL FROM:<'.$from.'>',[250]);
        $this->cmd('RCPT TO:<'.$to.'>',[250,251]);
        $this->cmd('DATA',[354]);
        $boundary='b'.bin2hex(random_bytes(8));
        $sub='=?UTF-8?B?'.base64_encode($subject).'?=';
        $alt=$text ?? strip_tags(str_replace(['<br>','<br/>','<br />'],"\n",$html));
        $msg="From: $fromHeader\r\nTo: <".$to.">\r\nSubject: $sub\r\nMIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n";
        $msg.="--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($alt));
        $msg.="--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($html));
        $msg.="--$boundary--\r\n.";
        fwrite($this->socket,$msg."\r\n"); $resp=$this->read(); if ((int)substr($resp,0,3)!==250) throw new RuntimeException('SMTP DATA failed: '.$resp);
        $this->cmd('QUIT',[221]); fclose($this->socket);
    }
}
