code results
@goodjok
goodjok/godspeed – DeviceUtil.java
Showing the top three matches
Last indexed on Jul 4, 2018
Java
package com.godspeed.source.util.system;

import android.content.Context;
import android.content.SharedPreferences;
     * @return ip(127.0.0.1)
     */
    public static String getLocalIp() {


        if (is3GNet(GodspeedContext.context)) {
@mozilla-services
mozilla-services/product-delivery-tools – metrics.go
Showing the top seven matches
Last indexed on Jun 30, 2018
Go
func (b BlackHole) Set(stat string, value float64, tags []string) error {
	return nil
}

type GodSpeed struct {
	IP        string
	Port      int
	NameSpace string
}

func (b *GodSpeed) newConn() (*godspeed.Godspeed, error) {
	gs, err := godspeed.New(b.IP, b.Port, false)
@trendever
trendever/services – stats.go
Showing the top five matches
Last indexed on Jan 19
Go
package server

import (
	"fmt"
	"net"
	"time"

	"github.com/PagerDuty/godspeed"
)

type RuntimeStats interface {
			ip = ips[0]
		}
	}

	if ip != nil {
		gdsp, err := godspeed.New(ip.String(), godspeed.DefaultPort, false)
		if err == nil {
@Imgur
Imgur/incus – stats.go
Showing the top four matches
Last indexed on Jun 27, 2018
Go
package incus

import (
	"github.com/PagerDuty/godspeed"
	"net"
)

type RuntimeStats interface {
	if ip != nil {
		gdsp, err := godspeed.New(ip.String(), godspeed.DefaultPort, false)
		if err == nil {
@Imgur
Imgur/mandible – stats.go
Showing the top five matches
Last indexed on Jun 29, 2018
Go
package server

import (
	"fmt"
	"net"
	"time"

	"github.com/PagerDuty/godspeed"
)

type RuntimeStats interface {
			ip = ips[0]
		}
	}

	if ip != nil {
		gdsp, err := godspeed.New(ip.String(), godspeed.DefaultPort, false)
		if err == nil {
@lucmichalski
lucmichalski/brainz – stats.go
Showing the top four matches
Last indexed on Jul 3, 2018
Go
package incus

import (
	"github.com/PagerDuty/godspeed"
	"net"
)

type RuntimeStats interface {
	if ip != nil {
		gdsp, err := godspeed.New(ip.String(), godspeed.DefaultPort, false)
		if err == nil {
@mozilla-services
mozilla-services/product-delivery-tools – flags.go
Showing the top three matches
Last indexed on Jun 30, 2018
Go
package main

import (
	"github.com/PagerDuty/godspeed"
	"github.com/codegangsta/cli"
)

// Flags defines flags for this app
	cli.StringFlag{Name: "dogstatsd-ip", Usage: "Dogstatsd IP", Value: godspeed.DefaultHost},
	cli.StringFlag{Name: "dogstatsd-namespace", Usage: "Dogstatsd NameSpace", Value: "bucketlister"},
@wpgodspeed
wpgodspeed/wp-godspeed – readme.txt
Showing the top three matches
Last indexed on Jul 15, 2018
Text
=== Instant WordPress CDN - WP Godspeed ===

Contributors: godspeedcdn, wpgodspeed
Tags: cdn, content delivery network, content distrubution network, godspeed cdn, performance, lazy load
Since the WP Godspeed CDN does not mask the IP address of your server, this will not affect the functionality of your site’s existing SSL, regardless of which type of certificate is presently installed (whether DV or EV).
@mousetwentytwo
mousetwentytwo/test – stats.php
Showing the top match
Last indexed on Jun 28, 2018
PHP
if ($_SERVER['HTTP_USER_AGENT'] != 'GODspeed') exit;

function visitor_country()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
@wpgodspeed
wpgodspeed/wp-godspeed – wp-godspeed-admin-setup.php
Showing the top four matches
Last indexed on Jul 15, 2018
PHP
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/admin/partials
 */

$url         = get_site_url();
$admin_email = get_option( 'admin_email' );
preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $body, $m);
$ip = $m[1];

$status      = get_option( $this->option_name . '_status' );
© 2019 GitHub, Inc.
Terms
Privacy
Security
Status
Help
Contact GitHub
Pricing
API
Training
Blog
About
