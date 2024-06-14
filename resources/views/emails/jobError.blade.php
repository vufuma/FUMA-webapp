<html>

<head>
    <h3>FUMA an error occurred</h3>
</head>

<body>

    <p>
        This is unfortunate! An error occurred during the process of your job (job ID: {{ $jobID }}, job title:
        {{ $jobtitle }}).<br />
        {{ $status }}
    </p>

    {!! $err_specific_msg !!}

    <h1>Make sure that your data is correctly formatted for FUMA</h1>
    <ol>
        <li>Make sure that there is a header (column name) in your input file.
            <ul>
                <li>The header should <strong>not</strong> start with a comment character (#). Any lines that starts
                    with # will be ignored.</li>
                <li>The number of column names should be equal to the number of columns in your input file.
                    <ul>
                        <li>sometimes the input file has a row index which means that there is one fewer column name
                            in the header as compared to when the actual data starts.</li>
                    </ul>
                </li>
            </ul>
        </li>

        <li>rsID if exists has to be in rsID format. See tutorial.</li>

        <li>Use <strong>gzip</strong> software to compress with <strong>.gz</strong> extension or <strong>ZIP</strong> software with <strong>.zip</strong> extension. 
			Make sure you haven't renamed the file manually. Use the proper compression software instead.</li>

        <li>The chromosome has to be numbers between 1 and 23 or X.</li>

        <li>Position values have to be integer (not in scientific notation) (see previous thread: <a
                href="https://groups.google.com/g/fuma-gwas-users/c/dmw_G0mvAM8/m/YmmZwHIxAgAJ">Error:003 inconsistency</a>)</li>

        <li>If your file contains chromosome and position, these have to be in <strong>hg19</strong> coordinates.</li>

        <li>Make sure that there is no missing data for the columns that are mandatory such as p-values (see previous
            thread: <a href="https://groups.google.com/g/fuma-gwas-users/c/_1wrXRoKD3w/m/8TZteeg0AwAJ">ERROR:magma</a> or <a
                href="https://groups.google.com/g/fuma-gwas-users/c/A7-5pJ8SGG0/m/3NMQTTkuDwAJ">ERROR:001</a> or <a
                href="https://groups.google.com/g/fuma-gwas-users/c/Eto1RbEYoc8/m/OXMPkbyTAgAJ">ERROR:magma</a>)</li>

        <li>If you specify the name of chromosome, position, etc... during submission, make sure that these names exist
            in your input file (see previous thread: <a href="https://groups.google.com/g/fuma-gwas-users/c/YqS31DKVwkQ/m/JcrOXmmSEwAJ">ERROR:001</a>)</li>

        <li>Make sure that the delimiter is consistent. In addition, Delimiter can be any of white space including
            single space, multiple space and tab. Because of this, each element including column names must not include
            any space (see previous thread: <a href="https://groups.google.com/g/fuma-gwas-users/c/F-zHFX5pW74/m/kaEFcFmjAAAJ">Help with ERROR:001</a>)
        </li>

        <li>Check your file to make sure that there is no quotation around each value. It should be for example 1
            instead of "1". This is usually caused when you save a file in R. To avoid this, one needs to set quote=F
            when saving a file in R (see previous thread: <a href="https://groups.google.com/g/fuma-gwas-users/c/E1Qtk1-4apc/m/6sDD5_g2AwAJ">ERROR:001</a>)
        </li>
    </ol>

    <p>
        To solve this issue, please follow the troubleshooting list: 
		<a href="https://groups.google.com/g/fuma-gwas-users/c/N3HCEXBJ8Iw">GUIDELINES ON TROUBLESHOOTING FUMA ERRORS</a>
		and <a href="https://groups.google.com/g/fuma-gwas-users/c/oVvZFhMpCY4">GUIDELINES ON FUMA ISSUES SUBMISSION</a>. 
        You can post questions, suggestions and bug reports on Google Forum:
        <a href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a><br /><br />
        FUMA development team<br />
        VU University Amsterdam<br />
        Dept. Complex Trait Genetics<br />
    </p>
</body>

</html>
