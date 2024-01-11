# This document test case when refactoring gwas_file.py script

## Issues with rsID
### Issue with rsID format
- Current behavior:
  - when the column for rsID is present, it will use it as is
  - when the column for rsID is present but not chr and pos, it will use the rsid to look up chr and pos
    - this is when the problem arises: if the rsid is not found in the database, there would be no variants. 
      - the problem is that we don't know if the rsid is not in the database, or that it's not formatted correctly
- Updated behavior:
  - basic check if the rsid starts with rs or not
  - in addition, check if ":" is present. The assumption is that when ":" is present, then it is in chr:pos format
    - there would be a warning
- Input file contains `chrcol`, `poscol`, `rsIDcol`, `pcol`, `eacol`, and `neacol` column, but `rsIDcol` is jibberish. 
```
cat CD_jibberish_rsIDcol.gwas
rsID    chr     pos     ref     alt     p
ABCrs3934834    1       1005806 C       T       0.71
ABCrs3766192    1       1017197 C       T       0.61
ABCrs3766191    1       1017587 C       T       0.8
ABCrs9442372    1       1018704 A       G       0.59
ABCrs10907177   1       1021346 A       G       0.78
ABCrs3737728    1       1021415 A       G       0.47
ABCrs9442398    1       1021695 A       G       0.28
ABCrs6687776    1       1030565 C       T       0.1
ABCrs6678318    1       1030633 G       A       0.11

### In the current version of `gwas_file.py`, it does not check whether the rsIDs are legit. The output is:
cat input.snps
chr     bp      non_effect_allele       effect_allele   rsID    p
1       1005806 T       C       ABCrs3934834    0.71
1       1017197 C       T       ABCrs3766192    0.61
1       1017587 T       C       ABCrs3766191    0.8
1       1018704 A       G       ABCrs9442372    0.59
1       1021346 G       A       ABCrs10907177   0.78
1       1021415 A       G       ABCrs3737728    0.47
1       1021695 A       G       ABCrs9442398    0.28
1       1030565 T       C       ABCrs6687776    0.1
1       1030633 A       G       ABCrs6678318    0.11
```
- However, this is an issue when rsID is used to look up chr & pos:
```
cat CD_jibberish_rsIDcol_2cols.gwas
rsID    p
ABCrs3934834    0.71
ABCrs3766192    0.61
ABCrs3766191    0.8
ABCrs9442372    0.59
ABCrs10907177   0.78
ABCrs3737728    0.47
ABCrs9442398    0.28
ABCrs6687776    0.1
ABCrs6678318    0.11
### Current script's output:
Either chr or pos is not provided
/var/www/tfumatest/scripts/gwas_file.py.bk:518: FutureWarning: Method .as_matrix will be removed in a future version. Use .values instead.
  gwas = gwas.as_matrix()
start chr1
start chr2
start chr3
start chr4
start chr5
start chr6
start chr7
start chr8
start chr9
start chr10
start chr11
start chr12
start chr13
start chr14
start chr15
start chr16
start chr17
start chr18
start chr19
start chr20
start chr21
start chr22
start chr23
There was no SNPs remained after formatting the input summary statistics.
```
- #TODO: Check if the user's supplied rsID is in the database. If not, convert to UID? This is computational intensive
- Tanya's updated version:
  - requiring that rsid has to start with rs or that it could be in the chr:pos or chr:pos:a1:a2 format 
```
# for: CD_jibberish_rsIDcol_2cols.gwas
INFO: Checking argument.
INFO: Parsing config variables.
INFO: Parsing header.
INFO: FUMA detects this to be your header:  rsID        p
INFO: Number of columns in header:  2
INFO: User defined columns:  {}
INFO: Resolving column names.
INFO: FUMA will use the following indices:
INFO: Index for  non_effect_allele column:
INFO: Index for  be column:
INFO: Index for  rsID column:  0
INFO: Index for  Ncol column:
INFO: Index for  se column:
INFO: Index for  pval column:  1
INFO: Index for  effect_allele column:
INFO: Index for  position column:
INFO: Index for  or column:
INFO: Index for  chromosome column:
INFO: Sanitize input.
INFO: Processing the input file.
INFO: Either chr or pos is not provided.
INFO: After sanitizing input gwas, there are  0  variants left.
There are no variants left after sanitizing input gwas.

# also:
cat input.snps.rm
rsID    p| Ignore: This line is mostly a header.
ABCrs3934834    0.71| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs3766192    0.61| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs3766191    0.8| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs9442372    0.59| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs10907177   0.78| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs3737728    0.47| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs9442398    0.28| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs6687776    0.1| Ignore: In the rsID column, rsID does not seem to be in the correct format
ABCrs6678318    0.11| Ignore: In the rsID column, rsID does not seem to be in the correct format
```

```
# for: CD_jibberish_rsIDcol_2cols_v2.gwas
INFO: Checking argument.
INFO: Parsing config variables.
INFO: Parsing header.
INFO: FUMA detects this to be your header:  rsID        p
INFO: Number of columns in header:  2
INFO: User defined columns:  {}
INFO: Resolving column names.
INFO: FUMA will use the following indices:
INFO: Index for  non_effect_allele column:
INFO: Index for  be column:
INFO: Index for  rsID column:  0
INFO: Index for  Ncol column:
INFO: Index for  se column:
INFO: Index for  pval column:  1
INFO: Index for  effect_allele column:
INFO: Index for  position column:
INFO: Index for  or column:
INFO: Index for  chromosome column:
INFO: Sanitize input.
1:123   0.71| WARNING: the rsid column does not start with rs. The colon (:) is detected so tt seems to be in the UID format (chr:pos or chr:pos:a1:a2). This might be a problem with database look up.
2:123   0.61| WARNING: the rsid column does not start with rs. The colon (:) is detected so tt seems to be in the UID format (chr:pos or chr:pos:a1:a2). This might be a problem with database look up.
3:123   0.8| WARNING: the rsid column does not start with rs. The colon (:) is detected so tt seems to be in the UID format (chr:pos or chr:pos:a1:a2). This might be a problem with database look up.
INFO: Processing the input file.
INFO: Either chr or pos is not provided.
INFO: After sanitizing input gwas, there are  3  variants left.
/var/www/tfumatest/scripts/gwas_file.py:523: FutureWarning: Method .as_matrix will be removed in a future version. Use .values instead.
  gwas = gwas.as_matrix()
start chr1
start chr2
start chr3
start chr4
start chr5
start chr6
start chr7
start chr8
start chr9
start chr10
start chr11
start chr12
start chr13
start chr14
start chr15
start chr16
start chr17
start chr18
start chr19
start chr20
start chr21
start chr22
start chr23
1
ERROR: There was no SNPs remained after formatting the input summary statistics.
```
### NA in rsIDcol 
- Issue reported hre: https://ctglab.backlog.com/view/FUMA-71
- rsIDcol will become empty
```
cat CD_rsID_NAs.gwas
rsID    chr     pos     ref     alt     p
NA      1       1005806 C       T       0.71
NA      1       1017197 C       T       0.61
NA      1       1017587 C       T       0.8
NA      1       1018704 A       G       0.59
NA      1       1021346 A       G       0.78
NA      1       1021415 A       G       0.47
NA      1       1021695 A       G       0.28
NA      1       1030565 C       T       0.1
NA      1       1030633 G       A       0.11
```
-> results:
```
cat input.snps
chr     bp      non_effect_allele       effect_allele   rsID    p
1       1005806 T       C               0.71
1       1017197 C       T               0.61
1       1017587 T       C               0.8
1       1018704 A       G               0.59
1       1021346 G       A               0.78
1       1021415 A       G               0.47
1       1021695 A       G               0.28
1       1030565 T       C               0.1
1       1030633 A       G               0.11
```
- Tanya's updated version:
  - rationale: if rsID is NA, the variants are thrown out & an explanation will be given
```
INFO: Checking argument.
INFO: Parsing config variables.
INFO: Parsing header.
INFO: FUMA detects this to be your header:  rsID        chr     pos     ref     alt     p
INFO: Number of columns in header:  6
INFO: User defined columns:  {}
INFO: Resolving column names.
INFO: FUMA will use the following indices:
INFO: Index for  non_effect_allele column:
INFO: Index for  be column:
INFO: Index for  rsID column:  0
INFO: Index for  Ncol column:
INFO: Index for  se column:
INFO: Index for  pval column:  5
INFO: Index for  effect_allele column:
INFO: Index for  position column:  2
INFO: Index for  or column:
INFO: Index for  chromosome column:  1
INFO: Sanitize input.
INFO: Both chr and pos are present. input gwas is being sorted by chr and pos
INFO: Processing the input file.
INFO: Chromosome column and position columns are detected.
INFO: Either the column for effect allele or non-effect allele is missing. Extract from the database
1
ERROR: There was no SNPs remained after formatting the input summary statistics.

# 
cat input.snps.rm
rsID    chr     pos     ref     alt     p| Ignore: This line is mostly a header.
NA      1       1005806 C       T       0.71| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1017197 C       T       0.61| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1017587 C       T       0.8| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1018704 A       G       0.59| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1021346 A       G       0.78| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1021415 A       G       0.47| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1021695 A       G       0.28| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1030565 C       T       0.1| Ignore: In the rsID column, rsID does not seem to be in the correct format
NA      1       1030633 G       A       0.11| Ignore: In the rsID column, rsID does not seem to be in the correct format
```

## Issues dealing with header
### 1. No header 
```
cat CD_no_header.gwas
rs3934834       1       1005806 C       T       0.71
rs3766192       1       1017197 C       T       0.61
rs3766191       1       1017587 C       T       0.8
rs9442372       1       1018704 A       G       0.59
rs10907177      1       1021346 A       G       0.78
rs3737728       1       1021415 A       G       0.47
rs9442398       1       1021695 A       G       0.28
rs6687776       1       1030565 C       T       0.1
rs6678318       1       1030633 G       A       0.11
```
-> current script's results:
```
P-value column was not found
```
- This is not a very useful message
- Updated script's results:
```
INFO: Checking argument.
INFO: Parsing config variables.
INFO: Parsing header.
INFO: FUMA detects this to be your header:  rs3934834   1       1005806 C       T       0.71
INFO: Number of columns in header:  6
INFO: User defined columns:  {}
INFO: Resolving column names.
INFO: FUMA will use the following indices:
INFO: Index for  non_effect_allele column:
INFO: Index for  be column:
INFO: Index for  rsID column:
INFO: Index for  Ncol column:
INFO: Index for  se column:
INFO: Index for  pval column:
INFO: Index for  effect_allele column:
INFO: Index for  position column:
INFO: Index for  or column:
INFO: Index for  chromosome column:
ERROR: P-value column was not found. A column for P-value is mandatory.
```

## Columns check
### 1. Chromosome column
- Current script:
  - converts any chromosome containing X to 23. So X is converted to 23, and so does XY, YX, XX. Is this what we want? See: /data/tfumatest/jobs/refactor_gwas_file/20230313/238434/
  - use method `isdigit()` to check if the string is a digit
    - `true` if 1, 2, 3, 01
    - `false` if 1.0, 2.0, 1.5
  - check if it's between 1 and 23 (inclusive)
- Updated script: retaining these checks for now

### 2. Position column
- Current script:
  - does not do anything
  - consequence:
    - when `chrcol`, `poscol`, `rsIDcol`, `pcol`, `eacol`, and `neacol` columns are present, this does not cause an error. 
    - an example of this issue is when the position is converted to scientific notation (1e6), this is problematic for downstream scripts (for example `getLD.py`). 
    - because FUMA currenlty does not
    - the desired behavior is to remove the variants where it's not a digit so that the users can investigate
- Updated script:
  - - Use method `isdigit()` to check if it's a digit

```
# Example input file to show that one of the variants has a scientific notation for the position
cat CD_pos_scientific_notation.gwas
cat CD_pos_scientific_notation.gwas
rsID    chr     pos     a1      a2      p
rs3934834       1       1e6     C       T       0.71
rs3766192       1       1017197 C       T       0.61
rs3766191       1       1017587 C       T       0.8
rs9442372       1       1018704 A       G       0.59
rs10907177      1       1021346 A       G       0.78
rs3737728       1       1021415 A       G       0.47
rs9442398       1       1021695 A       G       0.28
rs6687776       1       1030565 C       T       0.1
rs6678318       1       1030633 G       A       0.11
```
-> current FUMA results: returns it as is & does not remove this variant
```
cat input.snps
chr     bp      non_effect_allele       effect_allele   rsID    p
1       1e6     T       C       rs3934834       0.71
1       1017197 T       C       rs3766192       0.61
1       1017587 T       C       rs3766191       0.8
1       1018704 G       A       rs9442372       0.59
1       1021346 G       A       rs10907177      0.78
1       1021415 G       A       rs3737728       0.47
1       1021695 G       A       rs9442398       0.28
1       1030565 T       C       rs6687776       0.1
1       1030633 A       G       rs6678318       0.11
```
- Tanya's updated version:
```
# input.snps
cat input.snps
chr     bp      non_effect_allele       effect_allele   rsID    p
chr     pos     A2      A1      rsID    p
1       1017197 T       C       rs3766192       0.61
1       1017587 T       C       rs3766191       0.8
1       1018704 G       A       rs9442372       0.59
1       1021346 G       A       rs10907177      0.78
1       1021415 G       A       rs3737728       0.47
1       1021695 G       A       rs9442398       0.28
1       1030565 T       C       rs6687776       0.1
1       1030633 A       G       rs6678318       0.11

# 
cat input.snps.rm
rsID    chr     pos     a1      a2      p| Ignore: Value for the chromosome column is not a digit.
rs3934834       1       1e6     C       T       0.71| Ignore: Value for the position column is not a digit.
```
- Note that in the current script, when not all `chrcol`, `poscol`, `rsIDcol`, `pcol`, `eacol`, and `neacol` columns are present, it's going to return an error because it's trying to sort 
```
# example with missing rsid 
cat CD_pos_scientific_notation_rsidmissing.gwas
chr     pos     a1      a2      p
1       1e6     C       T       0.71
1       1017197 C       T       0.61
1       1017587 C       T       0.8
1       1018704 A       G       0.59
1       1021346 A       G       0.78
1       1021415 A       G       0.47
1       1021695 A       G       0.28
1       1030565 C       T       0.1
1       1030633 G       A       0.11

-> error with the current script
Traceback (most recent call last):
  File "/var/www/tfumatest/scripts/gwas_file_bk.py", line 437, in <module>
    tmp = tmp[np.lexsort((tmp[:,poscol].astype(int), tmp[:,chrcol].astype(int)))]
ValueError: invalid literal for long() with base 10: '1e6'
```
  - In the updated script, it won't return an error. Rather, any variants where the position is not a digit would be removed. 


### Pvalue column check
- Current script:
  - check if p value is a float
  - check if p value is between 0 and 1
  - check if p value starts with 0

## Additional todo
- #TODO: check if all variants are unique

## Summary: To discuss with Doug and Marijn
1. Currently FUMA converts any chromosome containing X to 23. So X is converted to 23, and so does XY, YX, XX. Is this what we want? See: `/data/tfumatest/jobs/refactor_gwas_file/20230313/238434/`
2. How do we want to deal with RSID? 
    1. Ideally we would cross-check the user’s rsid against the database. However, the way that Kyoko does it with tabix, it’s a bit computational intensive. Therefore, this needs a bit of development work
    2. My work-around:
        1. Check if it starts with rs
        2. If not, check if the : is in the string. My rationale is that usually it’s chr:pos or chr:pos:allele1:allele2.