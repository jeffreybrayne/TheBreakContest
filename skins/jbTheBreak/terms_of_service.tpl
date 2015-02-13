{if jrCore_module_is_active('jrPage')}
    {jrCore_list module="jrPage" order_by="_created desc" limit="1" search1="page_title_url like %terms%" search2="page_location = 0" template="tos_pp_row.tpl" assign="TOS"}
{/if}

{assign var="selected" value="tos"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="79" default="Terms Of Service" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="container">

<div class="row">

    <div class="col12 last">

    {if isset($TOS) && strlen($TOS) > 0}

        {$TOS}

    {else}

        <div class="title">
            {if jrUser_is_admin()}
                <div class="block_config">
                    <a href="{$jamroom_url}/page/admin/tools" title="update" href="{$jamroom_url}/page/admin/tools">{jrCore_icon icon="gear" size="16"}</a>
                </div>
            {/if}
            <h1>{$_conf.jrCore_system_name} {jrCore_lang skin=$_conf.jrCore_active_skin id="79" default="Terms Of Service"}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="Home"}</a> &raquo; {jrCore_lang skin=$_conf.jrCore_active_skin id="79" default="Terms Of Service"}
            </div>
        </div>

        <div class="body_1">

            {if jrUser_is_admin()}
                <span style="text-align: left;font-size: 10px; padding-left: 10px;">(Only site Admins will see this note.)</span>
                <div class="page_notice error">
                    To change the text on this page, either modify the terms_of_service.tpl template found in your active skin directory, or click the gear button to the right and create a new Terms Of Service page.<br>
                    <br>
                    <div style="text-align: center;">
                        <span class="bold">Note:</span>&nbsp;Make sure to name the new page <span class="bold">Terms Of Service</span>.
                    </div>
                </div>
                <br><br>
            {/if}

            <div class="p20">

                We reserve the right, in our sole discretion, to modify these Terms and any
                Service fees, at any time, effective upon the date we post a new set of Terms
                on the Service site. Your continued use of the Service constitutes your
                binding acceptance of these Terms, including any changes or modifications
                that we may make. If any part of these Terms or any future changes to these
                Terms are not acceptable to you, you may cancel your Service by contacting us
                through our support form.

                We also reserve the right, in our sole discretion, to restrict, suspend or
                terminate your access to all or to any part of the Service at any time for
                any reason without prior notice or liability. We may change, suspend or discontinue
                all or any aspect of the Service at any time, including the availability of any
                Service feature, database, or content, without prior notice or liability.

                We reserve the right to remove any material that you submit to the Service for
                any reason without prior notice to you and without liability to us. Our goal is to
                ensure timely processing services; however, we do not guarantee that your submission
                will be processed within the expected timeframe. We will not have any liability to you
                as a result of service outages that are caused by our maintenance on the servers or
                the technology that underlies the Service, failures of our service providers (including
                telecommunications, hosting and power providers) computer viruses, natural disasters
                or other destruction or damage of our facilities, an act of God, war, civil disturbance
                or other cause beyond our reasonable control.

                <br><br>
                <h3>Your content</h3>
                <br><br>

                By submitting material to the Service, you represent and warrant that:
                We, our customers and licensees shall not be required to make any payments with respect
                to material that you submit to our sites, including, but not limited to, payments to you,
                third parties, music publishers, mechanical rights agents, performance rights societies,
                persons who contributed to or appear in your materials, your licensors, unions or guilds;
                You have full right and power to enter into and perform under these Terms, and have
                secured all third-party consents, licenses and permissions necessary to enter into and
                perform under these Terms,
                The material that you submit to our sites does not contain "samples" of any third party's
                sound recording or musical composition and will not infringe on any third party's copyright,
                patent, trademark, trade secret or other proprietary rights, rights of publicity or privacy
                or moral rights;
                The material that you submit is not and will not violate any law, statute, ordinance or regulation;
                The material that you submit is not and will not be defamatory, trade libelous, pornographic
                or obscene, and You are at least eighteen years of age. By submitting sound recordings or musical
                compositions or other audio and/or audio-visual content to us, you grant us, our affiliates, and
                our business partners a worldwide, royalty-free, nonexclusive license to:

                <ul>
                    <li>publicly perform, publicly display, broadcast, encode, edit, alter, modify, reproduce, transmit,
                        manufacture, distribute and synchronize with visual images your material, in whole or in part,
                        alone or in compilation with content provided by third parties, through any medium now known or
                        hereafter devised for the purpose of demonstrating, promoting or distributing your material, to
                        users seeking to download or otherwise acquire it and/or (ii) storing the work in a remote
                        database accessible by users;</li>
                    <li>Make your material accessible as audio and/or video streams;</li>
                    <li>Use any trademarks, service marks or trade names incorporated into your material and use the
                        likeness of any individual whose performance or image is contained in your material.</li>
                </ul>

                <br><br>
                <h3>Protect your password and subscription</h3>
                <br><br>

                You agree to provide true, accurate, current and complete information about yourself as requested
                in the Service's registration process and to update your information. You may not reveal your
                subscription password to anyone else and you may not use anyone else's password. You are
                responsible for maintaining the confidentiality of your subscription account and password.
                Unauthorized access to the Service is a breach of these Terms and a violation of the law.

                <br><br>
                <h3>Requests for removal of listings</h3>
                <br><br>

                If you believe that material you own has been copied and made accessible in a manner that
                violates your intellectual property rights, please notify us immediately. We will consider such
                requests individually.

                <br><br>
                <h3>Third party sites and content</h3>
                <br><br>

                This Service contains links to other Internet sites that our business partners and other
                third parties own or operate. Your use of each of those sites is subject to the terms and conditions,
                if any, that each of those sites have posted. We have no control over third party sites and we are
                not responsible for any changes to or content on them. Our inclusion of any material in the Service's
                search database or a link on our sites is not an endorsement of that material or link or the companies
                that own or operate the material or linked sites.

                <br><br>
                <h3>Your conduct on the Service</h3>
                <br><br>

                In addition to our Site, certain material that you submit may, in our sole discretion, also become
                available to certain partners around the world. If we discover that you have manipulated the data or
                statistics for certain materials, we reserve the right to remove the product from our site and any of
                our Affiliates.  The content on the Service is intended for your personal, noncommercial use. All
                materials published on the Service, including, but not limited to, photographs, graphics, images,
                illustrations, sound clips and flash animation are protected by copyright. You may not modify, publish,
                transmit, participate in the transfer or sale of, reproduce, create new works from, distribute, perform
                display or in any way exploit any of the materials or content or the service in whole or part.

                If the Service contains bulletin board services, chat areas, news groups, forums, communities and/or
                message or communication facilities (collectively, the "Forums"), you agree to use any Forum only to send
                and receive messages and material that are proper and related to that particular Forum. Without limiting
                the foregoing, you agree that you will not (i) defame, abuse, harass, stalk, threaten or otherwise
                violate the legal right of others; (ii) publish, post, upload, distribute or disseminate any inappropriate,
                profane, defamatory, infringing, obscene, indecent or unlawful topic, name, material or information;
                (iii) upload files that contain viruses, corrupted files, or any other similar software or programs that
                may damage the operation of another person's computer; (iv) advertise or offer to sell any goods or
                services for any commercial purpose; (v) conduct or forward surveys, contests, pyramid schemes or chain
                letters; (vi) download any file posted by another user of a forum that you know or reasonably should know,
                cannot be legally distributed in such matter, (vii) falsify or delete any author attributions, legal or
                other proper notices or proprietary designations or labels of the origin or source of software or other
                material contained in a file that is uploaded; or (viii) restrict or inhibit any other user from using
                and enjoying the forum. We reserve the right to terminate your access to any or all of the forums at any
                time without notice for any reason whatsoever. If, in our sole discretion, you choose a username that is
                obscene, indecent, abusive or which might otherwise subject our site to public disparagement or scorn, we
                reserve the right, without prior notice to you, to automatically change your username, delete your posts
                from the Forums, deny you access to the Forums, or any combination of these options. If you continue to
                choose usernames that we find objectionable, we reserve the right to permanently terminate your access to
                the Forums, the Service or both.

                You will not use the Service for illegal purposes. Use of the Service is subject to existing laws and
                legal process, and nothing contained herein shall limit our right to comply with governmental, court and
                law enforcement requests or requirements relating to your use of the Service or information provided to
                or gathered by us with respect to such use.

                <br><br>
                <h3>Legal Policies and Notices</h3>
                <br><br>

                You hereby agree to indemnify, defend and hold the Service, and all of our officers, directors, owners,
                agents, information providers, affiliates and licensors (collectively, the "Parties") harmless from and
                against any and all liability, losses, costs and expenses (including attorneys' fees) incurred by any
                Party in connection with any claim arising out of (1) any use or alleged use of your account or password
                by any person, whether or not authorized by you, (2) any claim arising out of the material that you submit
                to the Service, including, but not limited to, claims for defamation, violation of rights of publicity
                and/or privacy, copyright infringement, trademark infringement and any claim or liability relating to the
                content, quality, or performance of materials that you submit to the Service. We reserve the right, at our
                own expense, to assume the exclusive defense and control of any matter otherwise subject to indemnification
                by you, and in such case, you agree to cooperate with our defense of such claim.

                The listing, or absence of listing, of any document in the Service's search database does not imply any
                warranty or guarantee by us, for any companies, products, or services described in such documents. We
                disclaim any and all responsibility or liability for the accuracy, content, completeness, legality,
                reliability, or operability or availability of information or material displayed in the Service's search
                results. We disclaim any responsibility for the deletion, failure to store, mis-delivery, or untimely delivery
                of any information or material. We disclaim any responsibility for any harm resulting from downloading or
                accessing any information or material on the World Wide Web or Internet using search results from the Service.

                WE DO NOT WARRANT THAT THE SERVICE WILL BE UNINTERRUPTED OR ERROR-FREE. IN ADDITION, WE DO NOT MAKE ANY WARRANTY
                AS TO THE RESULTS TO BE OBTAINED FROM USE OF THE SERVICE OR THE CONTENT. THE SERVICE AND THE CONTENT ARE
                DISTRIBUTED ON AN "AS IS, AS AVAILABLE" BASIS. ANY MATERIAL DOWNLOADED OR OTHERWISE OBTAINED THROUGH THE SERVICE
                IS DONE AT YOUR OWN DISCRETION AND RISK, AND YOU WILL BE SOLELY RESPONSIBLE FOR ANY POTENTIAL DAMAGES TO YOUR
                COMPUTER SYSTEM OR LOSS OF DATA THAT RESULTS FROM THE DOWNLOAD OF ANY SUCH MATERIAL. WE DO NOT MAKE ANY WARRANTIES
                OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING, WITHOUT LIMITATION, WARRANTIES OF TITLE OR IMPLIED WARRANTIES
                OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE, WITH RESPECT TO THE SERVICE, ANY CONTENT OR ANY PRODUCTS
                OR SERVICES SOLD THROUGH THE SERVICE. YOU EXPRESSLY AGREE THAT YOU WILL ASSUME THE ENTIRE RISK AS TO THE QUALITY
                AND PERFORMANCE OF THE SERVICE AND THE ACCURACY OR COMPLETENESS OF ITS CONTENT.
                WE SHALL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE
                USE OF OR INABILITY TO USE THE SERVICE, EVEN IF WE HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
                WE RESERVE THE RIGHT TO TERMINATE THE SERVICE AT ANY TIME WITHOUT NOTICE.

                Any controversy or claim arising out of or relating to these Terms or our sites will be settled by binding
                arbitration in accordance with the commercial arbitration rules of the American Arbitration Association.
                Any such controversy or claim shall be arbitrated on an individual basis, and shall not be consolidated in
                any arbitration with any claim or controversy of any other party. The arbitration shall be conducted in
                San Francisco, California, and judgment on the arbitration award may be entered in any court having
                jurisdiction thereof. Either you or we may seek any interim or preliminary relief from a court of competent
                jurisdiction in San Francisco, California necessary to protect the rights or property of you or the Party
                (or its agents, suppliers, and subcontractors) pending the completion of arbitration.

                These Terms constitute the entire agreement between you and the Parties with respect to the Service, and
                supersedes all previous written or oral agreements. If any part of these Terms is determined to be invalid
                or unenforceable pursuant to applicable law, then the invalid or unenforceable provision will be deemed
                superseded by a valid, enforceable provision that most closely matches the intent of the original provision
                and the remainder of the Terms shall continue in effect. Some states do not allow exclusion of implied warranties
                or limitation of liability for incidental or consequential damages, so the above limitations or exclusions may
                not apply to you. In such states, our liability and that of our third party content providers and their
                respective agents shall be limited to the greatest extent permitted by law.

            </div>

        </div>

    {/if}

    </div>

</div>

</div>

{jrCore_include template="footer.tpl"}
